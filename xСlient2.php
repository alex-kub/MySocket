<?php
require './Base/SocketClient.php';

use alexkub\Base\SocketClient;

$socket_client = new SocketClient([
	'address' => '127.0.0.1',
	'port' => 6666
]);

$in = "HEAD / HTTP/1.1\r\n";
$in .= "Host: www.example.com\r\n";
$in .= "Connection: Close\r\n\r\n";


$socket_client->connect();


while (true) {
	$socket_client->write(rand());
  echo $socket_client->read(1024) . "\n";
	sleep(3);
}

