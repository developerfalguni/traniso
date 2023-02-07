<?php

use Fabiang\Xmpp\Options;
use Fabiang\Xmpp\Client;
use Fabiang\Xmpp\Protocol\Roster;
use Fabiang\Xmpp\Protocol\Presence;
use Fabiang\Xmpp\Protocol\Message;

class Xmpp {
	function __construct() {
		
	}

	function send($host, $username, $password, $to, $message) {
		$options = new Options($host); //'tcp://localhost:5222'
		$options->setUsername($username)->setPassword($password)->setTo('localhost');
		$client = new Client($options);
		$client->connect();

		$msg = new Message;
		if (is_array($to)) {
			foreach ($to as $t) {
				$msg->setMessage($message)->setTo($t);
				$client->send($msg);
			}
		}
		else {
			$msg->setMessage($message)->setTo($to);
			$client->send($msg);
		}
	}
}
