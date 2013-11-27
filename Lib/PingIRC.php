<?php

class PingIRC {
	
	const TIMEOUT = 10;

	public static function ping($host, $port, $ssl = false){
		
		$return = true;

		$fp = @fsockopen($host, $port, $errno, $errstr, self::TIMEOUT);
		if (!$fp) {
			$return = "$errstr ($errno)";
		} else {
			fclose($fp);
			$client = new \Phergie\Irc\Client\React\Client();
			$connection = new \Phergie\Irc\Connection();
			$connection->setServerHostname($host);
			$connection->setServerPort($port);
			$connection->setNickname('MediaIRC');
			$connection->setUsername('MediaIRC');
			$connection->setRealname('MediaIRC');
			if ($ssl)
				$connection->setOption('transport', 'ssl');
			
			$client->on('irc.received', function() use ($client) {
				$client->getLoop()->stop();
			});
			$client->on('connect.error', function() use ($client, &$return) {
				$return = "Problem maintaining connection.";
				$client->getLoop()->stop();
			});
			$client->addTimer(self::TIMEOUT, function() use ($client, &$return){
				$return = "Either not an IRC server or wrong SSL setting.";
				$client->getLoop()->stop();
			});
			$client->run(array($connection));
		}
		
		return $return;
	}
	
}