<?php

class PingIRC {
	
	const TIMEOUT = 10;

	public static function ping($host, $port, $chan = null, $ssl = false){
		
		$return = true;
		
		if (!defined('STDERR'))
			define('STDERR', fopen('php://stderr', 'w'));

		$fp = @fsockopen($host, $port, $errno, $errstr, self::TIMEOUT);
		if (!$fp) {
			$return = "Could not connect to host.";
		} else {
			fclose($fp);
			$client = new \Phergie\Irc\Client\React\Client();
			$connection = new \Phergie\Irc\Connection();
			$connection->setServerHostname($host);
			$connection->setServerPort($port);
			$connection->setNickname('MediaPing'.rand(9,9999));
			$connection->setUsername('MediaIRC');
			$connection->setRealname('MediaIRC');
			if ($ssl)
				$connection->setOption('transport', 'ssl');
			
			$timer = $client->addTimer(self::TIMEOUT, function() use ($client, &$return){
				$return = "Timed out. Either not an IRC server or wrong SSL setting.";
				$client->getLoop()->stop();
			});
			
			$read_motd = false;
			$client->on('irc.received', function($message, $write) use ($client, &$timer, $chan, &$return, &$read_motd) {
				if ($client->isTimerActive($timer))
					$client->cancelTimer($timer);
				if (is_null($chan))
					$client->getLoop()->stop();
				if ($message['command'] == 'PING')
					$write->ircPong($message['params']['server1']);
				if ($message['command'] == 'ERROR') {
					$return = "There was a problem. Try again.";
					$client->getLoop()->stop();
				}
				if (isset($message['code'])){
					switch($message['code']){
						case 'RPL_MOTD': // Because RPL_ENDOFMOTD is sometimes delayed, making Ping slow
							if ($read_motd)
								break; // Only execute once to avoid flood after all the RPL_MOTD
							$read_motd = true;
						case 'RPL_ENDOFMOTD':
						case 'ERR_NOMOTD':
							$write->ircPart($chan);
							break;
						case 'ERR_NOSUCHCHANNEL':
							$return = "Specified channel doesn't exist.";
						case 'ERR_NOTONCHANNEL':
							$client->getLoop()->stop();
					}
				}
			});
			$client->on('connect.error', function() use ($client, &$return) {
				$return = "Problem maintaining connection.";
				$client->getLoop()->stop();
			});
			
			$client->run(array($connection));
		}
		
		return $return;
	}
	
}