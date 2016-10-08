<?php
namespace alexkub\Base;
require 'SocketBootstrap.php';

use Exception;

class SocketClient {
  use SocketBootstrap;   
  public $connected;

  public function __construct($args) {
    $this->bootstrap($args);
  }

    public function connect() {
      $this->connected = @socket_connect($this->socket,$this->address, $this->port);
      if (!$this->connected) $this->exception();
      return $this->connected;
   }

   public function disconnect( $how = 2 ) {
    if ($this->connected && !socket_shutdown($this->socket, $how))
      $this->exception();
   }

   public function write($msg) {
      if (!$bytes = socket_write( $this->socket, $msg, strlen($msg)))
        $this->exception();
      return $bytes;
   }

   public function read($bytes, $type = PHP_BINARY_READ) {
      if (!$from = socket_read($this->socket, $bytes, $type))
        $this->exception();
      return $from;
   }

   public function readLine($block=256)
   {
   	$trm = ["\r", "\n", "\0"];
   	$msg = "";

   	while(true) {
   		$msg .= $this->read($block, PHP_NORMAL_READ);
   		$lastChar = substr($msg, -1);

    	if (strlen($msg)==1 && in_array($lastChar, $trm)) {
    		//$msg = "";
    		continue;
    	}
    	if ( !in_array($lastChar, $trm)) {
    		continue;
    	}
    	break;
		}
		//return substr($msg, 0, -1)."\0";
		return substr($msg, 0, -1);
   	}

	public function readToEnd() {
		$lines = '';
		while ($this->connected) {
			$line = @$this->readLine();
			if (strlen($line)<2) break;
			$lines .=$line;
		};
		return $lines;
	}

	public function send($msg, $receive = false) {
		try {
			if (!$this->connected) $this->connect();
				$this->write($msg);
				if ($receive) return $this->readToEnd();
			
		}
		catch(Exception $ex) {
			echo "File:".$ex->getFile()."\n";
			echo "Line:".$ex->getLine()."\n";
			echo $ex->getTraceAsString()."\n";
			echo "Message:".$ex->getMessage();
		}
	}
}