<?php

namespace App\Models;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class Post extends Model
{
    private $table = 'posts';

    public function __construct()
    {
        parent::__construct();

        $sql = "CREATE TABLE IF NOT EXISTS $this->table (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            category VARCHAR(255),
            tags VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        if ($this->conn->query($sql) === false) {
            die("Error creating table: " . $this->conn->error);
        }
    }

    public function create(array $data)
    {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (title, content, category, tags) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $data['title'], $data['content'], $data['category'], $data['tags']);

        if ($stmt->execute() === false) {
            die("Error creating post: " . $stmt->error);
        }

        $result = $this->conn->query("SELECT * FROM $this->table WHERE id = " . $stmt->insert_id);

        return $result->fetch_assoc();
    }
}
