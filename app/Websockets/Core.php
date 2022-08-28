<?php

namespace App\Websockets;

use GuzzleHttp\Psr7\Uri;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Core implements MessageComponentInterface
{
    protected $seanceSubscribers;

    public function __construct()
    {
        $this->seanceSubscribers = [];
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {

        if ($id = $this->getPathId($conn)) {
            $this->seanceSubscribers[$id][$conn->resourceId] = $conn;
        }

    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        unset($this->seanceSubscribers[$this->getPathId($conn)][$conn->resourceId]);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    /**
     * Triggered when a client sends data through the socket
     * @param \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        foreach ($this->seanceSubscribers[$this->getPathId($from)] as $subscriber) {

            if ($subscriber->resourceId !== $from->resourceId) {
                $subscriber->send($msg);
            }

        }
    }

    protected function getPathId($connection)
    {
        $path = $connection->httpRequest->getUri()->getPath();

        if (str_contains($path, '/seances/')) {
            $position = strpos($path, '/seances/');
            $id = str_replace('/seances/', '', substr($path, $position));

            return $id ?? false;
        }

        return false;
    }
}
