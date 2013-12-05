<?php

App::uses('MediaLog', 'MediaLog');
App::uses('PingIRC', 'Lib');

class BotShell extends AppShell {
	
	public $uses = array('Bot', 'Link');
	
	// Stores server/chan information:
	//   array('irc.server.net:6667'=> array(
	//       'channels' => array(
	//           '#chan1' => array(
	//               'id' => 98, // Bot id
	//               'context' => array(), // Chat logging
	//               'pending' => array(
	//                   'lines' => 0,
	//                   'log' => MediaLog obj
	//               )
	//       ),
	//       'connection' => \Phergie\Irc\Connection()
	//   ))
	protected $servers = array();
	
	private $context_lines = 3; // Lines before and after posted links
	
	public function ping(){
		$port = 6697;
		$host = 'chat.freenode.net';
		var_dump(PingIRC::ping($host, $port, '#chan', true));
	}
	
	private function processContext(&$chan_array, $message){
		$chan_array['context'][] = $message;
		var_dump($chan_array['context']);
		if (count($chan_array['context']) > $this->context_lines * 2 + 1)
			array_shift($chan_array['context']);
		foreach ($chan_array['pending'] as $key => $pending){
			if ($pending['lines'] > 1){
				$chan_array['pending'][$key]['lines']--;
			} else {
				$context = implode("\n", $chan_array['context']);
				$pending['log']->save($chan_array['id'], $context);
				unset($chan_array['pending'][$key]);
			}
		}
	}
	
	// Used to resolve nickname conflicts
	private function incrementNick($nick){
		// TODO: proper incrementing
		return $nick."1";
	}
	
	public function start() {
		$this->out('Starting up MediaIRC bots...');

		$client = new \Phergie\Irc\Client\React\Client();
		
		$client->addPeriodicTimer(5, function() use ($client) {
			$this->out('TIMER!');
			// Fetch active bots with SQL, loop through to make connect array
			$bots = $this->Bot->find('all');
			foreach ($bots as $bot){
				$server = $bot['Bot']['server'];
				$chan = strtolower($bot['Bot']['channel']);
				if ($bot['Bot']['active']) {
					if (isset($this->servers[$server])){
						if (!isset($this->servers[$server]['channels'][$chan])){
							if ($this->servers[$server]['connection']){
								$this->servers[$server]['write']->ircJoin($chan);
							}
							$this->servers[$server]['channels'][$chan] = array(
									'id' => $bot['Bot']['id'],
									'context' => array(),
									'pending' => array()
								);
						}
					} else {
						list($host, $port) = explode(':', $server);
						$connection = new \Phergie\Irc\Connection();
						$connection->setServerHostname($host);
						$connection->setServerPort($port);
						$connection->setNickname('MediaIRC');
						$connection->setUsername('MediaIRC');
						$connection->setRealname('MediaIRC');
						if ($bot['Bot']['ssl'])
							$connection->setOption('transport', 'ssl');
						$client->addConnection($connection);
						$this->servers[$server] = array(
							'channels' => array(
								$chan => array(
									'id' => $bot['Bot']['id'],
									'context' => array(),
									'pending' => array()
								)
							),
							'connection' => null, // Null while not yet connected
						);
					}
				} else {
					// PART if still in channel array
					if (isset($this->servers[$server])){
						if (isset($this->servers[$server]['channels'][$chan])){
							if ($this->servers[$server]['connection']){
								$this->servers[$server]['write']->ircPart($chan);
							}
							unset($this->servers[$server]['channels'][$chan]);
						}
					}
				}
			}
		});
		
		$client->on('irc.received', function($message, $write, $connection, $logger) use ($client) {
			
			$logger->debug(var_export($message, true));
			$server = $connection->getServerHostname() . ":" . $connection->getServerPort();
			
			switch ($message['command']) {
				case "PING":
					$write->ircPong($message['params']['server1']);
					break;
				case "PRIVMSG":
					$receiver = strtolower($message['params']['receivers']);
					if ($receiver == strtolower($connection->getNickname())){
						// TODO: PM Commands
					} else {
						// Check for URLs
						$text = $message['params']['text'];
						$this->processContext($this->servers[$server]['channels'][$receiver],
								"<{$message['nick']}> $text");
						preg_match_all('/\b(?:(?:https?|ftp):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $text, $result, PREG_PATTERN_ORDER);
						$result = $result[0];
						// Log all matched URLs
						foreach ($result as $url){
							$log = MediaLog::loadUrl($url);
							if ($log){
								// Save $log to pending array to be saved later when context text is recorded
								$this->servers[$server]['channels'][$receiver]['pending'][] = array(
										'lines' => $this->context_lines,
										'log' => $log
									);
								$bot_id = $this->servers[$server]['channels'][$receiver]['id'];
								$logger->debug($receiver.' '.$bot_id." ". get_class($log)." - ".$url);
							}
						}
					}
					break;
				case "KICK":
					if ($message['params']['user'] == strtolower($connection->getNickname())){
						// Deactivate bot after being kicked twice
						$chan = $message['params']['channel'];
						$bot_id = $this->servers[$server]['channels'][$chan]['id'];
						$cachekey = $connection->getServerHostname() . $chan;
						if (Cache::read($cachekey, 'kickcounter')){
							$write->ircJoin($chan);
							$write->ircPrivmsg($chan, "Fine, you win.");
							$write->ircPart($chan);
							$this->Bot->id = $bot_id;
							$this->Bot->saveField('active', false);
						} else {
							$write->ircJoin($chan);
							$write->ircPrivmsg($chan, $message['nick'].", why do you hate me? :<");
							Cache::write($cachekey, true, 'kickcounter');
						}
					}
					break;
				case "ERROR":
					// TODO: have attempt counter to eventually give up
					$logger->debug('Connection to ' . $connection->getServerHostname() . ' lost, attempting to reconnect');
					$client->addConnection($connection);
					break;
				case "JOIN":
					break;
				default:
// 					$logger->debug(var_export($message, true));
			}

			if (isset($message['code'])){
				// Join channels
				// TODO: Check for ERR_NOSUCHCHANNEL or something (for when a channel is removed/unregistered)
				if (in_array($message['code'], array('RPL_ENDOFMOTD', 'ERR_NOMOTD'))) {
					foreach ($this->servers[$server]['channels'] as $chan => $data)
						$write->ircJoin($chan);
					$this->servers[$server]['connection'] = $connection;
					$this->servers[$server]['write'] = $write;
				}
				
				if ($message['code'] == 'ERR_NICKNAMEINUSE'){ // Maybe ERR_NICKCOLLISION too?
					$this->servers[$server]['connection'] = null;
					$connection->setNickname($this->incrementNick($connection->getNickname()));
					$write->ircQuit(); // Will automatically try to reconnect
				}
			}

		});
		
		$client->on('connect.error', function($message, $connection, $logger) use ($client) {
			$logger->debug('Connection to ' . $connection->getServerHostname() . ' lost, attempting to reconnect');
			$client->addConnection($connection);
		});
		
		$this->out('Running event loop...');
		$client->run(array());
	}
	
}
