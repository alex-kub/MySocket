<?php

namespace alexkub\Base;

use Exception;

trait SocketBootstrap {

   public $socket;
   protected $address;
   protected $port;

   public function address() {
      return $this->address;
   }

   public function port() {
      return $this->port;
   }

   public function resource() {
      return $this->socket;
   }
	
	protected function exception($message = false) {
		if ($message) {
			throw new Exception($message);
			return;
		}
		throw new Exception( socket_strerror(socket_last_error()) );
	}

	protected function bootstrap($args) {	
		
		if (!is_array($args))
			$this->exception('Settings in "__construct" is not <array>');

		if (!isset($args['address']))
			$this->exception("['address'] not defined");

		if (!isset($args['port']))
			$this->exception("['port'] not defined");

		$this->address = $args['address'];
		$this->port = $args['port'];

		$this->domain = 
				isset($args['domain']) ? $args['domain'] : AF_INET;

		$this->type = 
				isset($args['type']) ? $args['type'] : SOCK_STREAM;	

		$this->protocol = 
				isset($args['protocol']) ? $args['protocol'] : SOL_TCP;				

		if (!$this->socket = socket_create($this->domain, $this->type,
		 $this->protocol)) 
        $this->exception();     
	}
}

/*
class testSocket {
	use SocketAddons;

	public function __construct($args='') {
		$this->settings($args);
	}
}

try {
	$testSocket = new testSocket([
		'address' => '127.0.0.1',
		'port' => 8080
		]);	

}
catch(Exception $ex) {
			echo "File:".$ex->getFile()."\n";
			echo "Line:".$ex->getLine()."\n";
			echo $ex->getTraceAsString()."\n";
			echo "Message:".$ex->getMessage();
}
*/