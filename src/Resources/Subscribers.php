<?php

declare(strict_types=1);

namespace KiriMel\Resources;

use KiriMel\HttpClient;

/**
 * Subscribers resource client
 */
class Subscribers
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * List subscribers for a list
     */
    public function list(int $listId, array $params = []): array
    {
        return $this->httpClient->get("lists/{$listId}/subscribers", $params);
    }

    /**
     * List all subscribers (newly added)
     */
    public function list_all(array $params = []): array
    {
        return $this->httpClient->get("subscribers", $params);
    }

    /**
     * Get single subscriber
     */
    public function get(int $id): array
    {
        return $this->httpClient->get("subscribers/{$id}");
    }

    /**
     * Add subscriber to a list
     */
    public function create(array $data): array
    {
        return $this->httpClient->post("subscribers", $data);
    }

    /**
     * Update subscriber
     */
    public function update(int $id, array $data): array
    {
        return $this->httpClient->post("subscribers/{$id}", $data);
    }

    /**
     * Delete subscriber
     */
    public function delete(int $id): array
    {
        return $this->httpClient->delete("subscribers/{$id}");
    }

    /**
     * Unsubscribe subscriber
     */
    public function unsubscribe(int $id): array
    {
        return $this->httpClient->post("subscribers/{$id}/unsubscribe");
    }

    /**
     * Bulk unsubscribe
     */
    public function bulkUnsubscribe(array $subscriberIds): array
    {
        return $this->httpClient->post('subscribers/bulk-unsubscribe', [
            'subscriber_ids' => $subscriberIds
        ]);
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(array $subscriberIds): array
    {
        return $this->httpClient->post('subscribers/bulk-delete', [
            'subscriber_ids' => $subscriberIds
        ]);
    }

    /**
     * Get subscriber activity
     */
    public function activity(int $id): array
    {
        return $this->httpClient->get("subscribers/{$id}/activity");
    }

    /**
     * Get subscriber statistics
     */
    public function stats(int $id): array
    {
        return $this->httpClient->get("subscribers/{$id}/stats");
    }

    /**
     * Toggle VIP status
     */
    public function toggleVip(int $id): array
    {
        return $this->httpClient->post("subscribers/{$id}/toggle-vip");
    }

    /**
     * Search subscribers
     */
    public function search(string $query): array
    {
        return $this->httpClient->get('subscribers/search', ['q' => $query]);
    }

    /**
     * Add tag to subscriber
     */
    public function addTag(int $id, string $tag): array
    {
        return $this->httpClient->post("subscribers/{$id}/tags", ['tag' => $tag]);
    }

    /**
     * Remove tag from subscriber
     */
    public function removeTag(int $id, string $tag): array
    {
        return $this->httpClient->post("subscribers/{$id}/tags/{$tag}/remove");
    }

    /**
     * Import subscribers
     */
    public function import(int $listId, array $data): array
    {
        return $this->httpClient->post("lists/{$listId}/subscribers/import", $data);
    }
}
