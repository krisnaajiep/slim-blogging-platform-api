<?php

namespace App\Models;

use mysqli;

class Model
{
    protected $conn;

    public function __construct()
    {
        $this->connectDB();
    }

    private function connectDB()
    {
        // Create connection
        $this->conn = new mysqli(
            hostname: $_ENV['DB_HOST'],
            username: $_ENV['DB_USER'],
            password: $_ENV['DB_PASS'],
            port: $_ENV['DB_PORT']
        );

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Create database if not exists
        $sql = "CREATE DATABASE IF NOT EXISTS {$_ENV['DB_NAME']}";
        if ($this->conn->query($sql) === TRUE) {
            $this->conn->select_db($_ENV['DB_NAME']);
        } else {
            die("Error creating database: " . $this->conn->error);
        }
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}
