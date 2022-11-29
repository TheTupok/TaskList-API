<?php

    namespace Database;

    require "./core/services/date.service.php";

    use mysqli;
    use DateService;

    class DatabaseService
    {

        public function __construct()
        {
            $this->dateService = new DateService();
        }

        private function openDatabaseConn()
        {
            $mysqli = new mysqli("localhost", "root", "root", "tasktracker");
            if ($mysqli->connect_error) {
                die("Error database connection: " . $mysqli->connect_error);
            }

            return $mysqli;
        }

        public function getLastId($table)
        {
            $mysqli = $this->openDatabaseConn();
            $sql = "SELECT MAX(id) FROM $table";

            $result = $mysqli->query($sql);
            $row = $result->fetch_assoc();

            $mysqli->close();

            return $row['MAX(id)'];
        }

        public function getTaskList(): array
        {
            $mysqli = $this->openDatabaseConn();
            $sql = "SELECT * FROM tasklist";

            $taskList = array();

            $result = $mysqli->query($sql);
            if ($result->num_rows > 0) {
                foreach ($result as $row) {
                    $taskList[] = $row;
                }
            }

            $mysqli->close();
            return $taskList;
        }

        public function editTask($task): array
        {
            $mysqli = $this->openDatabaseConn();
            $members = json_encode($task['members'], JSON_UNESCAPED_UNICODE);
            $deadlineDate = $this->dateService->convertDate($task['deadline']);

            $dateOfCompleted = '';
            if ($task['status'] == 'Complete') {
                $dateOfCompleted = $this->dateService->getCurrentDate();
            }

            $sql = "UPDATE tasklist SET 
                    taskName = '{$task['taskName']}',
                    executor = '{$task['executor']}',
                    members = '$members',
                    deadline = '$deadlineDate',
                    dateOfCompleted = '$dateOfCompleted',
                    status = '{$task['status']}',
                    description = '{$task['description']}'
                    WHERE id = {$task['id']}";

            $mysqli->query($sql);

            $mysqli->close();
            return $this->getTaskList();
        }

        public function addTask(): array
        {
            $mysqli = $this->openDatabaseConn();
            $lastId = $this->getLastId('tasklist') + 1;
            $sql = "INSERT INTO tasklist 
                    (id, taskname, status, description) VALUES 
                    ($lastId, 'New task', 'Work', 'Description')";

            $mysqli->query($sql);

            $mysqli->close();
            return $this->getTaskList();
        }

        public function deleteTask($idTask): array
        {
            $mysqli = $this->openDatabaseConn();
            $sql = "DELETE FROM tasklist WHERE id = $idTask";

            $mysqli->query($sql);

            $mysqli->close();
            return $this->getTaskList();
        }
    }