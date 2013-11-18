<?php

class BotShell extends AppShell {
	
	public $uses = array('Bot');

// 	public function main() {
// 		$this->out('Sup bro.');
// 	}
	
	public function start() {
		$this->out('Starting up MediaIRC bots...');
		// Fetch active bots with SQL, loop through to make connect array
		$connection = new \Phergie\Irc\Connection();
		$connection->setServerHostname('irc.ghostsirc.net');
		$connection->setServerPort(6667);
// 		$connection->setPassword('password');
		$connection->setNickname('JimBot');
		$connection->setUsername('JimBot');
// 		$connection->setHostname('jimhost');
		$connection->setServername('jimservername');
		$connection->setRealname('jimmy');
// 		$connection->setOption('option', 'value');

		$client = new \Phergie\Irc\Client\React\Client();
		$client->on('connect.after.each', function($connection) {
			$this->out('Connected.');
// 			$write->ircJoin('#lea');
		});
		$client->addPeriodicTimer(30, function() {
			$this->out('30 seconds!!');
		});
		$client->on('irc.received', function($message, $write, $connection, $logger) use ($client) {
			
			switch ($message['command']) {
				case "PING":
					$write->ircPong($message['params']['server1']);
					break;
				case "PRIVMSG":
					if ($message['params']['receivers'] == "JimBot"){
						// PM Command
					} else {
						// Check for URLs
						$text = $message['params']['text'];
						preg_match_all('/\b(?:(?:https?|ftp):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $text, $result, PREG_PATTERN_ORDER);
						$result = $result[0];
						if (count($result) > 0) {
							$logger->debug(var_export($result, true));
						}
					}
					break;
				case "KICK":
					if ($message['params']['user'] == "JimBot"){
						$chan = $message['params']['channel'];
						$write->ircJoin($chan);
						$write->ircPrivmsg($chan, $message['nick'].", why do you hate me?");
					}
					break;
				case "ERROR":
					$logger->debug('Connection to ' . $connection->getServerHostname() . ' lost, attempting to reconnect');
					$client->addConnection($connection);
					break;
				case "JOIN":
					break;
				default:
					$logger->debug(var_export($message, true));
			}
			// Auto-join
			if (isset($message['code']) && in_array($message['code'], array('RPL_ENDOFMOTD', 'ERR_NOMOTD'))) {
				$write->ircJoin('#lea');
			}
		});
		$client->on('connect.error', function($message, $connection, $logger) use ($client) {
			$logger->debug('Connection to ' . $connection->getServerHostname() . ' lost, attempting to reconnect');
			$client->addConnection($connection);
		});
		$this->out('Running event loop...');
		$client->run(array($connection));
	}
	
}
