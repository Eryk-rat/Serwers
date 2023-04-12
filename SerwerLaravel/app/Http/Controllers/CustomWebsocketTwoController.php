<?php

namespace App\Http\Controllers;

use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use SplObjectStorage;

class CustomWebsocketTwoController implements MessageComponentInterface
{
    protected $clients;
    protected $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->clients = new SplObjectStorage;
        $this->loop = $loop;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo date('Y-m-d H:i:s') . " line:" . __LINE__ . " Client connected to CustomWebsocketTwoController " . $conn->resourceId . "\n";
        $this->clients->attach($conn);
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo date('Y-m-d H:i:s') . " line:" . __LINE__ . " => On close connection id " . $conn->resourceId . " \n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo date('Y-m-d H:i:s') . " line:" . __LINE__ . " => On error connection id " . $conn->resourceId . " \n";
        echo "*** " . $e->getLine() . ": " . $e->getMessage() . "\n";

        $this->clients->detach($conn);
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

}