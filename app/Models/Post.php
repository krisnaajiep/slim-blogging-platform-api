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

        return $this->getById($stmt->insert_id);
    }

    public function getById(int $id)
    {
        $result = $this->conn->query("SELECT * FROM $this->table WHERE id = $id");

        return $result->fetch_assoc();
    }

    public function getAll()
    {
        $result = $this->conn->query("SELECT * FROM $this->table");

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update(int $id, array $data)
    {
        $stmt = $this->conn->prepare("UPDATE $this->table SET title = ?, content = ?, category = ?, tags = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $data['title'], $data['content'], $data['category'], $data['tags'], $id);

        if ($stmt->execute() === false) {
            die("Error updating post: " . $stmt->error);
        }

        return $this->getById($id);
    }

    public function delete(int $id)
    {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() === false) {
            die("Error deleting post: " . $stmt->error);
        }

        return true;
    }
}
