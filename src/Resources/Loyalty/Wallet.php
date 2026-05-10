<?php

declare(strict_types=1);

namespace KiriMel\Resources\Loyalty;

use KiriMel\LoyaltyHttpClient;

/**
 * Wallet resource for Loyalty API
 */
class Wallet
{
    private LoyaltyHttpClient $httpClient;

    public function __construct(LoyaltyHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get wallet balance
     */
    public function balance(array $params): array
    {
        return $this->httpClient->get('api/loyalty/wallet/balance', $params);
    }

    /**
     * Recalculate wallet balance from ledger
     */
    public function recalculate(array $data): array
    {
        return $this->httpClient->post('api/loyalty/wallet/recalculate', $data);
    }
}
