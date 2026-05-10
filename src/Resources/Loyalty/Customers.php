<?php

declare(strict_types=1);

namespace KiriMel\Resources\Loyalty;

use KiriMel\LoyaltyHttpClient;

/**
 * Customers resource for Loyalty API
 */
class Customers
{
    private LoyaltyHttpClient $httpClient;

    public function __construct(LoyaltyHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Register a new customer
     *
     * @param array $data Customer data
     *   - phone: string (required) - Customer phone number with country code
     *   - name: string (required) - Customer name
     *   - email: string (optional) - Customer email
     *   - birth_date: string (optional) - Birthday in YYYY-MM-DD format
     *   - qr_code: string (optional) - QR code for lookup
     *   - tags: array (optional) - Initial tags
     * @return array
     */
    public function register(array $data): array
    {
        return $this->httpClient->post('api/loyalty/customers/register', $data);
    }

    /**
     * Look up customer by phone, QR code, or customer ID
     *
     * @param array $params Lookup parameters
     *   - phone: string - Look up by phone number
     *   - qr_code: string - Look up by QR code
     *   - customer_id: string - Look up by customer ID
     * @return array
     */
    public function lookup(array $params): array
    {
        return $this->httpClient->get('api/loyalty/customers/lookup', $params);
    }

    /**
     * Look up customer by email address
     *
     * @param string $email Customer email address
     * @return array
     */
    public function lookupByEmail(string $email): array
    {
        return $this->httpClient->get('api/loyalty/customers/lookup-by-email', ['email' => $email]);
    }

    /**
     * Get customer profile by ID
     *
     * @param string|int $customerId Customer ID
     * @return array
     */
    public function get(string|int $customerId): array
    {
        return $this->httpClient->get("api/loyalty/customers/{$customerId}");
    }

    /**
     * Get customer transactions
     *
     * @param string|int $customerId Customer ID
     * @param array $params Optional parameters (limit, offset, type)
     * @return array
     */
    public function transactions(string|int $customerId, array $params = []): array
    {
        return $this->httpClient->get("api/loyalty/customers/{$customerId}/transactions", $params);
    }

    /**
     * Manually adjust customer points
     *
     * @param string|int $customerId Customer ID
     * @param array $data Adjustment data
     *   - points: int (required) - Points to add (positive) or subtract (negative)
     *   - reference: string (required) - Reference for this adjustment
     *   - description: string (optional) - Description
     *   - adjusted_by: string (optional) - Name of person making adjustment
     * @return array
     */
    public function adjust(string|int $customerId, array $data): array
    {
        return $this->httpClient->post("api/loyalty/customers/{$customerId}/adjust", $data);
    }

    /**
     * Get customer tier information
     *
     * @param string|int $customerId Customer ID
     * @return array
     */
    public function tier(string|int $customerId): array
    {
        return $this->httpClient->get("api/loyalty/customers/{$customerId}/tier");
    }

    /**
     * List customers with pagination
     *
     * @param array $params Query parameters (page, per_page, search, tier, etc.)
     * @return array
     */
    public function list(array $params = []): array
    {
        return $this->httpClient->get('api/loyalty/customers', $params);
    }
}
