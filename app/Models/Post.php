<?php

namespace App\Models;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Class Post
 * 
 * This class represents the Post model and provides methods to interact with the 'posts' table in the database.
 * It includes methods to create, read, update, and delete posts, as well as to retrieve all posts or search for posts by a term.
 */
final class Post extends Model
{
    /**
     * The name of the table associated with the Post model.
     *
     * @var string
     */
    private string $table = 'posts';

    /**
     * Constructor for the Post model.
     * 
     * This constructor initializes the database connection and ensures that the 'posts' table exists.
     * If the table does not exist, it will be created with the appropriate columns.
     */
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

    /**
     * Create a new post in the database.
     *
     * This method inserts a new post into the 'posts' table with the provided data.
     * It returns the newly created post.
     *
     * @param array $data An associative array containing the post data (title, content, category, tags)
     * @return array The newly created post as an associative array.
     */
    public function create(array $data): array
    {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (title, content, category, tags) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $data['title'], $data['content'], $data['category'], $data['tags']);

        if ($stmt->execute() === false) {
            die("Error creating post: " . $stmt->error);
        }

        return $this->getById($stmt->insert_id);
    }

    /**
     * Retrieve a post by its ID.
     *
     * This method fetches a post from the 'posts' table based on the provided ID.
     * It returns the post as an associative array.
     *
     * @param integer $id The ID of the post to retrieve.
     * @return array|null The post as an associative array or null if not found.
     */
    public function getById(int $id): array|null
    {
        $result = $this->conn->query("SELECT * FROM $this->table WHERE id = $id");

        return $result->fetch_assoc();
    }

    /**
     * Retrieve all posts or search for posts by a term.
     *
     * This method fetches all posts from the 'posts' table. If a search term is provided,
     * it filters the posts by the term, searching in the title, content, category, and tags.
     *
     * @param string|null $term The search term to filter posts by title, content, category, or tags.
     * @return array An array of posts. If a search term is provided, it returns the filtered posts.
     */
    public function getAll(string $term = null): array
    {
        $sql = "SELECT * FROM $this->table";

        if ($term) {
            $sql .= " WHERE title LIKE '%$term%' OR content LIKE '%$term%' OR category LIKE '%$term%' OR tags LIKE '%$term%'";
        }

        $result = $this->conn->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update an existing post in the database.
     *
     * This method updates the post with the provided ID using the new data.
     * It returns the updated post.
     *
     * @param integer $id The ID of the post to update.
     * @param array $data An associative array containing the post data (title, content, category, tags)
     * @return array|null The updated post as an associative array or null if not found.
     */
    public function update(int $id, array $data): array|null
    {
        $stmt = $this->conn->prepare("UPDATE $this->table SET title = ?, content = ?, category = ?, tags = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $data['title'], $data['content'], $data['category'], $data['tags'], $id);

        if ($stmt->execute() === false) {
            die("Error updating post: " . $stmt->error);
        }

        return $this->getById($id);
    }

    /**
     * Delete a post by its ID.
     *
     * This method deletes a post from the 'posts' table based on the provided ID.
     * It returns true if the deletion was successful.
     *
     * @param integer $id The ID of the post to delete.
     * @return true Returns true if the post was successfully deleted.
     */
    public function delete(int $id): true
    {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() === false) {
            die("Error deleting post: " . $stmt->error);
        }

        return true;
    }
}
