<?php
 
set_time_limit(0);
 
$NULL           = NULL;
$address        = "127.0.0.1";
$port           = 5555;
$max_clients    = 10;
$client_sockets = array();
$master         = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$res            = true;
 
$res &= @socket_bind($master, $address, $port);
$res &= @socket_listen($master);
 
if(!$res)
{
    die ("Невозможно привязать и прослушивать $address: $port\n");
}
 
$abort = false;
$read = array($master);
 
while(!$abort)
{
    $num_changed = socket_select($read, $NULL, $NULL, 0, 10);
    /* Изменилось что-нибудь? */
    if ($num_changed) 
    {
        /* Изменился ли главный сокет (новое подключение) */
        if(in_array($master, $read))
        { 
                if(count($client_sockets) < $max_clients)
                {
                        $client_sockets[]= socket_accept($master);
                        echo "Принято подключение (" . count($client_sockets)  . " of $max_clients)\n";
                }
        }       
        /* Цикл по всем клиентам с проверкой изменений в каждом из них */
        foreach($client_sockets as $key => $client)
        { 
            /* Новые данные в клиентском сокете? Прочитать и ответить */
            if(in_array($client, $read))
            {
                $input = socket_read($client, 1024);
         
                if($input === false)
                {
                    socket_shutdown($client);
                    unset($client_sockets[$key]); 
                }
                else
                {
                    $input = trim($input);
           
                    if (!@socket_write($client, "Вы сказали: $input\n") )
                    {
                        socket_close($client);
                        unset ( $client_sockets[$key] ) ;
                    }
                }
                 
                if($input == 'exit')
                { 
                    socket_shutdown($master);
                    $abort = true;
                }
         
            }// END IF in_array
       
        } // END FOREACH
     
    } // END IF ($num_changed)
     
    $read = $client_sockets;
    $read[] = $master;
} // END WHILE
