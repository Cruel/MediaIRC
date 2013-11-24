<?php

App::uses('MediaLog', 'MediaLog');

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
	
	public function test(){
// 		$url ="http://global.fncstatic.com/static/managed/img/Leisure/2009/jcvd-volvo-660.jpg";
// 		$url = "http://fddgfsdgfsdgdgf.com/";
		$url = "http://youtu.be/E4aqo6iIAiA?t=3s";
		$bot_id = 99;
		$log = MediaLog::loadUrl($url);
		if ($log){
			$log->save($bot_id, "context");
		}
		var_dump(get_class($log));
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
	
	public function start() {
		$this->out('Starting up MediaIRC bots...');

		$client = new \Phergie\Irc\Client\React\Client();
		
		$client->addPeriodicTimer(5, function() use ($client) {
			$this->out('TIMER!');
			// Fetch active bots with SQL, loop through to make connect array
			$bots = $this->Bot->find('all');
			foreach ($bots as $bot){
				$server = $bot['Bot']['server'];
				$chan = $bot['Bot']['channel'];
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
		
		$client->addPeriodicTimer(30, function() use ($client) {
			// TODO: Update cache with current connections for site stats page?
		});
		
		$client->on('irc.received', function($message, $write, $connection, $logger) use ($client) {
			
// 			$logger->debug(var_export($message, true));
			$server = $connection->getServerHostname() . ":" . $connection->getServerPort();
			
			switch ($message['command']) {
				case "PING":
					$write->ircPong($message['params']['server1']);
					break;
				case "PRIVMSG":
					$receiver = $message['params']['receivers'];
					if ($receiver == "MediaIRC"){
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
								$write->ircPrivmsg($receiver, $bot_id ." ". get_class($log)." - ".$url);
							}
						}
					}
					break;
				case "KICK":
					if ($message['params']['user'] == "MediaIRC"){
						$chan = $message['params']['channel'];
						$cachekey = $connection->getServerHostname() . $chan;
						if (Cache::read($cachekey, 'kickcounter')){
							$write->ircJoin($chan);
							$write->ircPrivmsg($chan, "Fine, you win.");
							$write->ircPart($chan);
						} else {
							$write->ircJoin($chan);
							$write->ircPrivmsg($chan, $message['nick'].", why do you hate me? :<");
							Cache::write($cachekey, true, 'kickcounter');
						}
					}
					break;
				case "ERROR":
					$logger->debug('Connection to ' . $connection->getServerHostname() . ' lost, attempting to reconnect');
					$client->addConnection($connection);
					break;
				case "JOIN":
					break;
				default:
// 					$logger->debug(var_export($message, true));
			}
			// Auto-join
			if (isset($message['code']) && in_array($message['code'], array('RPL_ENDOFMOTD', 'ERR_NOMOTD'))) {
				foreach ($this->servers[$server]['channels'] as $chan => $data)
					$write->ircJoin($chan);
				$this->servers[$server]['connection'] = $connection;
				$this->servers[$server]['write'] = $write;
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
