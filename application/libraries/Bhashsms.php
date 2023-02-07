<?php

use GuzzleHttp\Client;

class Bhashsms {
	public static $_url;
	public static $_get;
	public static $_client;
	
	function __construct() {
		self::$_url = 'http://bhashsms.com/api/';
		self::$_get = [
			'user' => Settings::get('bhashsms_user', 'USERNAME'),
			'pass' => Settings::get('bhashsms_password', 'PASSWORD'),
		];
		self::$_client = new Client();
	}
	
	/**
	* API To Check Balance
	* http://bhashsms.com/api/checkbalance.php?user=userid&pass=password
	*/
	public static function balance() {

		$response = self::$_client->get(self::$_url.'checkbalance.php?'.http_build_query(self::$_get));
		if ($response->getStatusCode() != 200) {
			return NULL;
		}
		return $response->getBody()->read(1024);
	}

	/**
	* API FOR All Sender ID
	* http://bhashsms.com/api/getsenderids.php?user=userid&pass=password
	*/
	public static function getSenderID() {
		$response = self::$_client->get(self::$_url.'getsenderids.php?'.http_build_query(self::$_get));
		if ($response->getStatusCode() != 200) {
			return NULL;
		}
		return $response->getBody()->read(1024);
	}

	/**
	* API To Request Sender ID
	* http://bhashsms.com/api/addsenderid.php?user=9427567676&senderid=SENDER&type=dnd/ndnd 	  	 
	*/
	public static function addSenderID($sender_id, $priority = 'ndnd') {
		self::$_get['senderid'] = $sender_id;
		self::$_get['type']     = $priority;
		
		$response = self::$_client->get(self::$_url.'addsenderid.php?'.http_build_query(self::$_get));
		if ($response->getStatusCode() != 200) {
			return NULL;
		}
		return $response->getBody()->read(1024);
	}

	/**
	* API FOR DELIVERY REPORT
	* http://bhashsms.com/api/recdlr.php?user=9427567676&msgid=MSGIDOFMESSAGE&phone=9********&msgtype=MessageType
	* MessageType - dnd for promo and ndnd for trans. For MessageID=S.45657, Recipient=91********** , msgid would be S.45657
	*/

	/**
	* API FOR SINGLE SMS
	* http://bhashsms.com/api/sendmsg.php?user=userid&pass=password&sender=060000&phone=7698945785&text=IDEX Rocks&priority=dnd&stype=normal
	* Note : smstype - normal/flash , Priority - ndnd/dnd , Mobile Number without 91
	* 
	* API FOR MULTIPLE SMS
	* http://bhashsms.com/api/sendmsg.php?user=userid&pass=password&sender=IDEX&phone=MobileNo1,MobileNo2..&text=Test SMS&priority=Priority&stype=smstype
	* Note : smstype - normal/flash , Priority - ndnd/dnd , Mobile Number without 91
	*/
	public static function send($sender, $to, $message, $priority = 'ndnd', $smstype = 'normal') {
		self::$_get['sender']   = $sender;
		self::$_get['phone']    = $to;
		self::$_get['text']     = $message;
		self::$_get['priority'] = $priority;
		self::$_get['stype']    = $smstype;

		$response = self::$_client->get(self::$_url.'sendmsg.php?'.http_build_query(self::$_get));
		if ($response->getStatusCode() != 200) {
			return NULL;
		}
		return $response->getBody()->read(1024);
	}

	/**
	* SCHEDULE API FOR SMS
	* http://bhashsms.com/api/schedulemsg.php?user=userid&pass=password&sender=IDEX&phone=MobileNo1,MobileNo2..&text=Test SMS&priority=Priority&stype=smstype&time=Scheduletime
	* Note : smstype - normal/flash , Priority - ndnd/dnd , Mobile Number without 91 ,
	* Time format - YYYY-MM-DD HH:MM, 
	* HH:MM is 24 hour clock. Eg : 2010-06-04 18:24
	*/
	public static function sendLater($sender, $to, $message, $time, $priority = 'ndnd', $smstype = 'normal') {
		self::$_get['sender']   = $sender;
		self::$_get['phone']    = $to;
		self::$_get['text']     = $message;
		self::$_get['time']     = $time;
		self::$_get['priority'] = $priority;
		self::$_get['stype']    = $smstype;

		$response = self::$_client->get(self::$_url.'schedulemsg.php?'.http_build_query(self::$_get));
		if ($response->getStatusCode() != 200) {
			return NULL;
		}
		return $response->getBody()->read(1024);
	}
}
