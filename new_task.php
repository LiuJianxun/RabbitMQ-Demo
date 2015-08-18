<?php

/**
 * PHP amqp(RabbitMQ) Demo-1
 * @author  yuansir <yuansir@live.cn/yuansir-web.com>
 */
$exchangeName = 'demo';
$queueName = 'hello';
$routeKey = 'hello';
$message = empty($argv[1]) ? 'Hello World!' : ' '.$argv[1];

$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");

try {
        $channel = new AMQPChannel($connection);
        $exchange = new AMQPExchange($channel);
        $exchange->setName($exchangeName);
        $queue = new AMQPQueue($channel);
        $queue->setName($queueName);
        $exchange->publish($message, $routeKey);
        print_r("[x] Sent $message \n");
} catch (AMQPConnectionException $e) {
        print_r($e);
        exit();
}
$connection->disconnect();