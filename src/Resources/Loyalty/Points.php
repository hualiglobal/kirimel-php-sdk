<?php

declare(strict_types=1);

namespace KiriMel\Resources\Loyalty;

use KiriMel\LoyaltyHttpClient;

/**
 * Points resource for Loyalty API
 */
class Points
{
    private LoyaltyHttpClient $httpClient;

    public function __construct(LoyaltyHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Award points to customer
     */
    public function earn(array $data): array
    {
        return $this->httpClient->post('api/loyalty/points/earn', $data);
    }

    /**
     * Preview points redemption
     */
    public function previewRedeem(array $data): array
    {
        return $this->httpClient->post('api/loyalty/points/preview-redeem', $data);
    }

    /**
     * Commit points redemption
     */
    public function commitRedeem(array $data): array
    {
        return $this->httpClient->post('api/loyalty/points/commit-redeem', $data);
    }

    /**
     * Reverse a points transaction
     */
    public function reverse(array $data): array
    {
        return $this->httpClient->post('api/loyalty/points/reverse', $data);
    }
}
