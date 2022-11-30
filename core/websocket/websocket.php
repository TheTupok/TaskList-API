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

        private function getRowValueAndSendResponse($from): void
        {
            $valueRow = $this->dbService->getRowValue();
            $response = ['typeOperation' => 'getRowValue', 'response' => $valueRow];
            $this->sendMessageToConn($response, $from);
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

            if ($typeOperation == 'getRowValue') {
                $this->getRowValueAndSendResponse($from);
            }
            if ($typeOperation == 'getTaskList') {
                $taskList = $this->dbService->getTaskList($msg['pageData']);
                $response = ['typeOperation' => $typeOperation, 'response' => $taskList];

                $this->sendMessageToConn($response, $from);
            }
            if ($typeOperation == 'editTask') {
                $this->dbService->editTask($msg['request']);
            }
            if ($typeOperation == 'newTask') {
                $this->dbService->addTask();
                $this->getRowValueAndSendResponse($from);
            }
            if ($typeOperation == 'deleteTask') {
                $this->dbService->deleteTask($msg['request']);
                $this->getRowValueAndSendResponse($from);
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