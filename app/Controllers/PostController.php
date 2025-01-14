<?php

namespace App\Controllers;

use App\Models\Post;
use App\Validators\PostValidator;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * Class PostController
 * 
 * This controller handles the CRUD operations for blog posts.
 * It includes methods to list all posts, create a new post, 
 * show a single post by ID, update a post by ID, and delete a post by ID.
 * 
 * @package App\Controllers
 */
class PostController
{
    /**
     * The Post model instance used for database operations.
     *
     * @var Post The Post model instance used for database operations.
     */
    private Post $model;

    /**
     * PostController constructor.
     * Initializes the Post model instance.
     */
    public function __construct()
    {
        $this->model = new Post();
    }

    /**
     * Lists all blog posts, optionally filtered by a search term.
     *
     * @param Request $request The HTTP request object containing query parameters and other request data.
     * @param Response $response The HTTP response object used to send the response back to the client.
     * @param array $args The route parameters, typically including the post ID for specific post operations.
     * @return ResponseInterface The HTTP response object containing the list of blog posts in JSON format.
     */
    public function index(Request $request, Response $response, array $args): ResponseInterface
    {
        $term = $request->getQueryParams()['term'] ?? null;

        $posts = $this->model->getAll($term);

        foreach ($posts as $key => $post) {
            $posts[$key] = $this->formatPost($post);
        }

        $response->getBody()->write(json_encode($posts));
        return $response;
    }

    /**
     * Creates a new blog post.
     *
     * @param Request $request The HTTP request object containing query parameters and other request data.
     * @param Response $response The HTTP response object used to send the response back to the client.
     * @param array $args The route parameters, typically including the post ID for specific post operations.
     * @return ResponseInterface The HTTP response object containing the list of blog posts in JSON format.
     */
    public function create(Request $request, Response $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];

        $validation = PostValidator::validate($data);
        if ($validation->hasValidationErrors()) {
            $response->getBody()->write(json_encode($validation->getValidationErrors()));
            return $response->withStatus(400);
        }

        $data['tags'] = json_encode($data['tags']);
        $post = $this->model->create($data);

        $response->getBody()->write(json_encode($this->formatPost($post)));
        return $response->withStatus(201);
    }

    /**
     * Shows a single blog post by ID.
     *
     * @param Request $request The HTTP request object containing query parameters and other request data.
     * @param Response $response The HTTP response object used to send the response back to the client.
     * @param array $args The route parameters, typically including the post ID for specific post operations.
     * @return ResponseInterface The HTTP response object containing the list of blog posts in JSON format.
     */
    public function show(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        $post = $this->model->getById($id);

        if (!$post) {
            $response->getBody()->write(json_encode(['message' => 'Post not found']));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($this->formatPost($post)));
        return $response;
    }

    /**
     * Updates an existing blog post by ID.
     *
     * @param Request $request The HTTP request object containing query parameters and other request data.
     * @param Response $response The HTTP response object used to send the response back to the client.
     * @param array $args The route parameters, typically including the post ID for specific post operations.
     * @return ResponseInterface The HTTP response object containing the list of blog posts in JSON format.
     */
    public function update(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $data = $request->getParsedBody() ?? [];

        $validation = PostValidator::validate($data);
        if ($validation->hasValidationErrors()) {
            $response->getBody()->write(json_encode($validation->getValidationErrors()));
            return $response->withStatus(400);
        }

        $data['tags'] = json_encode($data['tags']);
        $post = $this->model->update($id, $data);

        if (!$post) {
            $response->getBody()->write(json_encode(['message' => 'Post not found']));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($this->formatPost($post)));
        return $response;
    }

    /**
     * Deletes an existing blog post by ID.
     *
     * @param Request $request The HTTP request object containing query parameters and other request data.
     * @param Response $response The HTTP response object used to send the response back to the client.
     * @param array $args The route parameters, typically including the post ID for specific post operations.
     * @return ResponseInterface The HTTP response object containing the list of blog posts in JSON format.
     */
    public function delete(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        $post = $this->model->getById($id);

        if (!$post) {
            $response->getBody()->write(json_encode(['message' => 'Post not found']));
            return $response->withStatus(404);
        }

        $this->model->delete($id);

        return $response->withStatus(204);
    }

    /**
     * Formats a blog post for JSON response.
     *
     * @param array $post The blog post data to format.
     * @return array The formatted blog post data.
     */
    private function formatPost(array $post): array
    {
        $post['tags'] = json_decode($post['tags']);

        $created_at = new \DateTime($post['created_at']);
        $updated_at = new \DateTime($post['updated_at']);

        $post['created_at'] = $created_at->format('Y-m-d\TH:i:s\Z');
        $post['updated_at'] = $updated_at->format('Y-m-d\TH:i:s\Z');

        return $post;
    }
}
