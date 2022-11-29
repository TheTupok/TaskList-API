<?php

    namespace Socket;

    require "./core/database/database.service.php";

    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    use Database\DatabaseService;

    class WebSocket implements MessageComponentInterface
    {
        private \SplObjectStorage $clients;
        private DatabaseService $dbService;

        public function __construct()
        {
            $this->clients = new \SplObjectStorage;
            $this->dbService = new DatabaseService();
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

            if($typeOperation == 'getTaskList') {
                $taskList = $this->dbService->getTaskList();
                $response = ['typeOperation' => $typeOperation, 'response' => $taskList];

                $this->sendMessageToConn($response, $from);
            }
            if($typeOperation == 'editTask') {
                $taskList = $this->dbService->editTask($msg['request']);
                $response = ['typeOperation' => 'getTaskList', 'response' => $taskList];

                $this->sendMessageToConn($response, $from);
            }
            if($typeOperation == 'newTask') {
                $taskList = $this->dbService->addTask();
                $response = ['typeOperation' => 'getTaskList', 'response' => $taskList];

                $this->sendMessageToConn($response, $from);
            }
            if($typeOperation == 'deleteTask') {
                $taskList = $this->dbService->deleteTask($msg['request']);
                $response = ['typeOperation' => 'getTaskList', 'response' => $taskList];

                $this->sendMessageToConn($response, $from);
            }
        }

        public function onClose(ConnectionInterface $conn)
        {
            $this->clients->detach($conn);
            echo "Отключение пользователя $conn->resourceId \n";
        }

        public function onError(ConnectionInterface $conn, \Exception $e)
        {
            echo "Есть ошибка: {$e->getMessage()}\n";
        }
    }