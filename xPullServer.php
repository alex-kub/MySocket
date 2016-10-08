<?php
require 'Base\SocketServer.php';
require 'MyThread.php';
use alexkub\Base\SocketServer;


try {
  $server = new SocketServer([
    "address" => "127.0.0.1",
    "port" => 5555
  ]);

}
catch(\Exception $ex) {
  echo "File:".$ex->getFile()."\n";
  echo "Line:".$ex->getLine()."\n";
  echo $ex->getTraceAsString()."\n";
  echo "Message:".$ex->getMessage();
}