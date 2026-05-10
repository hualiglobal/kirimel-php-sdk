<?php

declare(strict_types=1);

namespace KiriMel\Resources\Loyalty;

use KiriMel\LoyaltyHttpClient;

/**
 * Vouchers resource for Loyalty API
 */
class Vouchers
{
    private LoyaltyHttpClient $httpClient;

    public function __construct(LoyaltyHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Create voucher batch
     */
    public function createBatch(array $data): array
    {
        return $this->httpClient->post('api/loyalty/vouchers/batches', $data);
    }

    /**
     * List voucher batches
     */
    public function listBatches(array $params = []): array
    {
        return $this->httpClient->get('api/loyalty/vouchers/batches', $params);
    }

    /**
     * Issue voucher to customer
     */
    public function issue(array $data): array
    {
        return $this->httpClient->post('api/loyalty/vouchers/issue', $data);
    }

    /**
     * Redeem voucher
     */
    public function redeem(array $data): array
    {
        return $this->httpClient->post('api/loyalty/vouchers/redeem', $data);
    }

    /**
     * Get voucher details by code
     */
    public function get(string $code): array
    {
        return $this->httpClient->get("api/loyalty/vouchers/{$code}");
    }
}
