<?php

namespace App\Http\Controllers;


use React\Http\Client\Client;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;
use Illuminate\Support\Facades\Log;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class MyCustomWebSocketHandler implements MessageComponentInterface
{

    public function onOpen(ConnectionInterface $connection)
    {
    $loop = new LoopInterface ;
    $client = new Client($loop );
    Log::info('połączenie z ws użytkownika: ' . $client);
    //$client->connection = $connection;
    //$this->clients->attach($client);
    return $client;

      
    }
    
    public function onClose(ConnectionInterface $connection)
    {
        // TODO: Implement onClose() method.
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        Log::info('połączenie z ws użytkownika: error ');
    }

    public function onMessage(ConnectionInterface $connection, MessageInterface $msg)
    {
        // TODO: Implement onMessage() method.
    }
}