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
            $members = json_encode($task['members']);
            $deadlineDate = $this->dateService->convertDate($task['deadline']);

            $sql = "UPDATE tasklist SET 
                    taskName = '{$task['taskName']}',
                    executor = '{$task['executor']}',
                    members = '$members',
                    deadline = '$deadlineDate',
                    status = '{$task['status']}',
                    description = '{$task['description']}'
                    WHERE id = {$task['id']}";

            $mysqli->query($sql);

            return $this->getTaskList();
        }
    }