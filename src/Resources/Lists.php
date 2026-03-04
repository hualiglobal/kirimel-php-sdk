<?php

declare(strict_types=1);

namespace KiriMel\Resources;

use KiriMel\HttpClient;

/**
 * Lists resource client
 */
class Lists
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * List all lists
     */
    public function list(array $params = []): array
    {
        return $this->httpClient->get('lists', $params);
    }

    /**
     * Get single list
     */
    public function get(int $id): array
    {
        return $this->httpClient->get("lists/{$id}");
    }

    /**
     * Create list
     */
    public function create(array $data): array
    {
        return $this->httpClient->post('lists', $data);
    }

    /**
     * Update list
     */
    public function update(int $id, array $data): array
    {
        return $this->httpClient->post("lists/{$id}", $data);
    }

    /**
     * Delete list
     */
    public function delete(int $id): array
    {
        return $this->httpClient->delete("lists/{$id}");
    }

    /**
     * Get list statistics
     */
    public function stats(int $id): array
    {
        return $this->httpClient->get("lists/{$id}/stats");
    }
}
