<?php

declare(strict_types=1);

namespace KiriMel\Tests;

use KiriMel\Client;
use KiriMel\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for Email API and Loyalty API
 *
 * Note: These tests require valid API credentials.
 * Set these environment variables before running:
 *
 * For Email API:
 *   KIRIMEL_API_KEY=your_email_api_key
 *
 * For Loyalty API:
 *   KIRIMEL_LOYALTY_API_KEY=your_api_key
 *   KIRIMEL_LOYALTY_KEY_SECRET=your_key_secret
 */
class ApiIntegrationTest extends TestCase
{
    private ?Client $client = null;

    protected function setUp(): void
    {
        $apiKey = $_ENV['KIRIMEL_API_KEY'] ?? null;
        $loyaltyApiKey = $_ENV['KIRIMEL_LOYALTY_API_KEY'] ?? null;
        $loyaltyKeySecret = $_ENV['KIRIMEL_LOYALTY_KEY_SECRET'] ?? null;

        if (!$apiKey) {
            $this->markTestSkipped('KIRIMEL_API_KEY environment variable not set');
        }

        $config = ['api_key' => $apiKey];

        if ($loyaltyApiKey && $loyaltyKeySecret) {
            $config['api_key'] = $loyaltyApiKey;
            $config['key_secret'] = $loyaltyKeySecret;
        }

        $this->client = new Client($config);
    }

    /**
     * Test Email API: List mailing lists
     */
    public function testEmailApiLists(): void
    {
        $result = $this->client->lists()->list();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test Email API: List campaigns
     */
    public function testEmailApiCampaigns(): void
    {
        $result = $this->client->campaigns()->list();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test Email API: List subscribers
     */
    public function testEmailApiSubscribers(): void
    {
        $result = $this->client->subscribers()->list_all();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test Loyalty API: Requires loyalty credentials
     */
    public function testLoyaltyApiCustomerLookup(): void
    {
        if (!$_ENV['KIRIMEL_LOYALTY_API_KEY'] ?? null) {
            $this->markTestSkipped('KIRIMEL_LOYALTY_API_KEY not set');
        }

        // Try to lookup a customer (will return 404 if not found, but proves auth works)
        try {
            $result = $this->client->loyaltyCustomers()->lookup([
                'phone' => '+60123456789'
            ]);
            $this->assertIsArray($result);
        } catch (ApiException $e) {
            // 404 is expected for non-existent customer
            // 401 would mean auth failed
            $this->assertNotEquals(401, $e->getHttpCode(), 'Authentication should succeed');
        }
    }

    /**
     * Test Loyalty API: Get wallet balance
     */
    public function testLoyaltyApiWalletBalance(): void
    {
        if (!$_ENV['KIRIMEL_LOYALTY_API_KEY'] ?? null) {
            $this->markTestSkipped('KIRIMEL_LOYALTY_API_KEY not set');
        }

        try {
            $result = $this->client->loyaltyWallet()->balance([
                'customer_id' => 'test_customer_123'
            ]);
            $this->assertIsArray($result);
        } catch (ApiException $e) {
            // 404 is expected for non-existent customer
            $this->assertNotEquals(401, $e->getHttpCode(), 'Authentication should succeed');
        }
    }

    /**
     * Test Loyalty API: HMAC signature calculation
     */
    public function testLoyaltyHmacSignatureCalculation(): void
    {
        if (!$_ENV['KIRIMEL_LOYALTY_API_KEY'] ?? null) {
            $this->markTestSkipped('KIRIMEL_LOYALTY_API_KEY not set');
        }

        // This test verifies HMAC signature is being calculated correctly
        // If auth works (404 instead of 401), signature is valid
        try {
            $result = $this->client->loyaltyPoints()->previewRedeem([
                'customer_id' => 'test_customer_123',
                'points_to_redeem' => 100
            ]);
            $this->assertIsArray($result);
        } catch (ApiException $e) {
            // 404 = customer not found (auth worked)
            // 401 = signature invalid (auth failed)
            $this->assertNotEquals(401, $e->getHttpCode(), 'HMAC signature should be valid');
        }
    }
}
