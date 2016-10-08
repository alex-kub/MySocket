<?php

require 'Base\SocketServer.php';

use alexkub\Base\SocketServer;

try {
	$socket_server = new SocketServer([
		'address' => '127.0.0.1',
		'port' => 5555
		]);
	$socket_server->start();
}
catch(\Exception $ex) {
	echo "File:".$ex->getFile()."\n";
	echo "Line:".$ex->getLine()."\n";
	echo $ex->getTraceAsString()."\n";
	echo "Message:".$ex->getMessage();
}





