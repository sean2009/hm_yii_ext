<?php
class RabbitMQClient{
	
	
	public function run($conn_args){
		$e_name = 'e_linvo'; //交换机名 
		//$q_name = 'q_linvo'; //无需队列名 
		$k_route = 'key_1'; //路由key 
		 
		//创建连接和channel 
		$conn = new AMQPStreamConnection('192.168.0.156',15672,'xp','xp');   
		if (!$conn->connect()) {   
		    die("Cannot connect to the broker!\n");   
		}   
		$channel = new AMQPChannel($conn);   
		 
		//消息内容 
		$message = "TEST MESSAGE! 测试消息！";
		 
		//创建交换机对象    
		$ex = new AMQPExchange($channel);   
		$ex->setName($e_name);   
		 
		//发送消息 
		//$channel->startTransaction(); //开始事务  
		for($i=0; $i<5; ++$i){ 
		    echo "Send Message:".$ex->publish($message, $k_route)."\n";  
		} 
		//$channel->commitTransaction(); //提交事务 
		
		$conn->disconnect(); 
	}
}