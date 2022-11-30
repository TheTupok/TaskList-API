# TaskList-API

The server is made on PHP 8.1. This server is needed to work with TaskList-Client (https://github.com/TheTupok/TaskList-Client)

# How to start

php index.php start server on machine

# Server Features

The server communicates with the client via a websocket (Ratchet). Works with all data through mysql. Mysql configured to display by paginator and sort from client side

The server starts on port 8080, but it can be changed in the index.php file


# Dump MySQL
unzip the sql file from zip. After that, create a new table in your database and import this dump there via the console (mysql -u {username} -p {nameDatabase} < {path}/dump-mysql.sql)
[dump_mysql.zip](https://github.com/TheTupok/TaskList-API/files/10123923/dump_mysql.zip)
