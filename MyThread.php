<?php
namespace alexkub\Tools;
require 'Base\SocketServer.php';
use alexkub\Base\SocketServer;
use Thread;

class SocketServerThread extends  Thread {

  protected $arg;

  public function __construct($arg) {
    $this->arg = $arg;
  }

  public function run() {
    try {
      $server = new SocketServer($this->arg);
      $server->start();
    }
    catch(\Exception $ex) {
      echo "File:".$ex->getFile()."\n";
      echo "Line:".$ex->getLine()."\n";
      echo $ex->getTraceAsString()."\n";
      echo "Message:".$ex->getMessage();
    }
  }
}

$server1 = new SocketServerThread(
  ['address' => '127.0.0.1',
    'port' => 5555]);
$server1->start();

$server2 = new SocketServerThread(
  ['address' => '127.0.0.1',
    'port' => 6666]);
$server2->start();






