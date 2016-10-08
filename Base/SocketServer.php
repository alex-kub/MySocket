<?php
namespace alexkub\Base;

require 'SocketBootstrap.php';
require 'AcceptedClient.php';

use alexkub\Base\AcceptedClient as Client;


class SocketServer {
	use SocketBootstrap;

	public function __construct($args) {

  	$this->bootstrap($args);

   	$this->max_client = 
   			isset($args['max_client']) ? $args['max_client'] : 10;

		if (!socket_bind($this->socket, $this->address, $this->port))
				$this->exception();

		$backlog = 5;
		if (!socket_listen($this->socket, $backlog))
				$this->exception();
	}

  protected $list_clients = [];

  public function start() {

    echo "Start Server {$this->address}:{$this->port}\n";

    $read[] = $this->socket;

    while (true) { // <1>
		  if (socket_select($read, $NULL, $NULL, 0, 10)) {
//
				if (in_array($this->socket, $read)) $this->onConnect();

        foreach($this->list_clients as $key => $client) {
          if (!in_array($client->socket, $read)) continue;
					$data = @socket_read($client->socket, 1024);

					if (!$data) {
						$this->onClose($client);
						socket_shutdown($client->socket);
						unset($this->list_clients[$key]);
						continue;
					}
          $this->onData($client, $data);
        }
			}  //if (socket_select(...

      $read = array($this->socket);
      foreach($this->list_clients as $client) {
        $read[] = $client->socket;
      }
		}	// while(true) <1>
	}	

/*
Вызывается, когда поступает запрос на соединение от клиента
*/
	public function onConnect() {
//    <! Добавить ID клиента!!!!!!  !>
		if (count($this->list_clients) > $this->max_client)
         $this->exception("Exceeded connection limit max_client = ".
            $this->max_client);

		if (!$socket = socket_accept($this->socket))
		   $this->exception();

      $client = new Client($socket);
      $this->list_clients[] = $client;
      $this->onOpen($client);
	}

/*
Вызывается, когда соединение успешно установлено
и клиент добавлен в список
*/
    protected function onOpen(Client $client) {
      echo "onOpen:\n";
  	   echo "New client:\n";
  	   echo $client->resource()."\n";
  	   echo "{$client->address()}:{$client->port()}";
  	   echo "\n---------------------\n";
    }

/*
Вызывается при закрытие соединения Клиентом
<! Нужен метод, который  вызывается,
когда разрыв инициируется сервером !>
*/
    protected function onClose(Client $client) {
  	  echo "onClose:\n".
        $client->address()."\nwith ". $client->resource();
      echo "\n--------------------------------\n";
    }

/*
Вызывается, когда приходят данные от клиента размером не более 1024 байт <! В настройки !>
*/
    protected function onData(Client $client, $data) {
      echo "onData:\n";
      echo "from {$client->address()}:{$client->port()}\n";
      echo $data;
      foreach($this->list_clients as $the_client) {
        $the_client->write("<<" . $data . ">>");
      }

      echo "\n-----------------\n";
    }
/*
Вызывается, когда из данных формируется сообщение,
ограниченное терминатором в настройках Клиента
<! В настройки сервера указать терминатор сообщения !>
*/
    protected function onMessage(Client $client, $message) {
      echo "onMessage:\n";
  	  echo "Client: <".$client->address().">\n";
  	  echo "Message:\n";
  	  echo $message;
  	  echo "---------------------\n";
    }
}