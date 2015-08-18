<?php

/**
 * PHP amqp(RabbitMQ) Demo-1
 * @author  yuansir <yuansir@live.cn/yuansir-web.com>
 */
$exchangeName = 'demo';
$queueName = 'hello';
$routeKey = 'hello';

$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
$channel = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->declare();
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->declare();
$queue->bind($exchangeName, $routeKey);

print_r("[*] Waiting for messages. To exit press CTRL+C \n");
while (TRUE) {
    $queue->consume('callback');
}
$connection->disconnect();

function callback($envelope, $queue) {
    $msg = $envelope->getBody();
    // print_r(" [x] Received:" . $msg."\n");
    file_put_contents("/Library/WebServer/www/rabbitmq-demo/worker_demo_log.txt",date("Y-m-d H:i:s",time())." [x] Received:" . $msg."\r\n",FILE_APPEND);
    sleep(substr_count($msg,'.'));
    $queue->nack($envelope->getDeliveryTag());
}