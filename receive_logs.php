<?php

/**
 * PHP amqp(RabbitMQ) Demo-3
 * @author  yuansir <yuansir@live.cn/yuansir-web.com>
 */
$exchangeName = 'logs';

$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
$channel = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_FANOUT);
$exchange->declare();
$queue = new AMQPQueue($channel);
$queue->setFlags(AMQP_EXCLUSIVE);
$queue->declare();
$queue->bind($exchangeName, '');

print_r('[*] Waiting for messages. To exit press CTRL+C'."\n");
while (TRUE) {
    $queue->consume('callback');
}
$connection->disconnect();

function callback($envelope, $queue) {
    $msg = $envelope->getBody();
    print_r(date("Y-m-d H:i:s",time())." [x] Received:" . $msg."\r\n");
    $queue->nack($envelope->getDeliveryTag());
}