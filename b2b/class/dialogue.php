<?php
require_once dirname(__FILE__) . '/../../app/Mage.php';
umask(0);
Mage::app('default');

date_default_timezone_set('NZ');

class Dialogue
{
	function insert($sender, $receiver, $sku, $message)
	{
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		
        $query = "insert into b2b_dialogue(sender, receiver, sku, time, message)
			VALUES ('" . $sender . "', '" . $receiver . "', '" . $sku . "', '" . date("Y-m-d H:i:s") . "', '" . $message . "');";	
		$writeConnection->query($query);		
	}
	
	function get($sku, $customer)
	{
        $resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		
        $sql    = "select * from b2b_dialogue where (sender = '" . $customer . "' or receiver = '" . $customer . "') and sku = '" . $sku . "' order by time desc";	
        $result = $readConnection->fetchAll($sql);

		$messages = array();	
		foreach ($result as $row) {
			$message = new StdClass;
			$message->datetime = $row["time"];
			$message->message = $row["message"];
			$message->sender = $row["sender"];
			$message->receiver = $row["receiver"];
			array_push($messages, $message);
		}
		return $messages;
	}
}
?>