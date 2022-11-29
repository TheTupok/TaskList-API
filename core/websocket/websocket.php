<?php

    namespace Socket;

    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;

    class WebSocket implements MessageComponentInterface
    {

        private \SplObjectStorage $clients;

        public function __construct()
        {
            $this->clients = new \SplObjectStorage;
        }

        public function sendMessageToConn($msg, $conn)
        {
            foreach ($this->clients as $client) {
                if ($conn == $client) {
                    $client->send(json_encode($msg));
                }
            }
        }

        public function onOpen(ConnectionInterface $conn)
        {
            $this->clients->attach($conn);
            echo "Новое подключение ($conn->resourceId)\n";
        }

        public function onMessage(ConnectionInterface $from, $msg)
        {
            $msg = json_decode($msg, true);
            $typeOperation = $msg['typeOperation'];
        }

        public function onClose(ConnectionInterface $conn)
        {
            $this->clients->detach($conn);
            echo "Отключение пользователя $conn->resourceId \n";
        }

        public function onError(ConnectionInterface $conn, \Exception $e)
        {
//            echo "Есть ошибка: {$e->getMessage()}\n";
//            $conn->close();
        }
    }