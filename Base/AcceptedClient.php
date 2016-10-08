<?php
namespace alexkub\Base;

require_once 'SocketBootstrap.php';

class AcceptedClient {
  use SocketBootstrap;

   public $buffer;
   public $size_buff;

   public $delimiter;

  public function __construct($resource, $size_buff = 1024, $delimiter="\r\n\r\n" ) {
    if (!is_resource($resource))
      $this->exception('<arg> in aClient::_construct is not resource!');

    $this->socket = $resource;

    socket_getsockname($resource, $this->address, $this->port);
    $this->size_buff = $size_buff;
    $this->delimiter = $delimiter;
  }

  public function add($data) {
    $this->buffer.=$data;
    // <Добавить обрезку по размеру $this->buff_size>
    if (
      substr( $this->buffer, -strlen($this->delimiter)) == $this->delimiter
    ) return true;

    return false;
  }

  public function write($msg) {
    return @socket_write( $this->socket, $msg, strlen($msg));
  }
}

/*
$client = new aClient('', 1024, "\r\n");
echo $client->add("12345\r\n\r\n");
*/