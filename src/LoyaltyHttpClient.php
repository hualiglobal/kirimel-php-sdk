<?php

declare(strict_types=1);

namespace KiriMel;

use KiriMel\Exceptions\ApiException;
use KiriMel\Exceptions\AuthenticationException;
use KiriMel\Exceptions\RateLimitException;
use KiriMel\Exceptions\ValidationException;

/**
 * HTTP Client for Loyalty API with HMAC SHA256 signature authentication
 */
class LoyaltyHttpClient
{
    private string $baseUrl;
    private ?string $apiKey;
    private ?string $keySecret;
    private int $timeout;
    private int $retries;
    private ?object $logger = null;

    public function __construct(array $config = [])
    {
        $this->baseUrl = rtrim($config['base_url'] ?? 'https://kirimel.com', '/');
        $this->apiKey = $config['api_key'] ?? null;
        $this->keySecret = $config['key_secret'] ?? null;
        $this->timeout = $config['timeout'] ?? 30;
        $this->retries = $config['retries'] ?? 3;
        $this->logger = $config['logger'] ?? null;
    }

    public function setLogger(object $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Make a GET request
     */
    public function get(string $path, array $params = []): array
    {
        $url = $this->buildUrl($path, $params);
        return $this->request('GET', $url);
    }

    /**
     * Make a POST request
     */
    public function post(string $path, array $data = []): array
    {
        return $this->request('POST', $this->buildUrl($path), $data);
    }

    /**
     * Make a PUT request
     */
    public function put(string $path, array $data = []): array
    {
        return $this->request('PUT', $this->buildUrl($path), $data);
    }

    /**
     * Make a DELETE request
     */
    public function delete(string $path): array
    {
        return $this->request('DELETE', $this->buildUrl($path));
    }

    /**
     * Build URL with query parameters
     */
    private function buildUrl(string $path, array $params = []): string
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Get current timestamp in ISO 8601 format (UTC)
     */
    private function getTimestamp(): string
    {
        return gmdate('Y-m-d\TH:i:s\Z');
    }

    /**
     * Calculate HMAC SHA256 signature
     */
    private function calculateSignature(string $timestamp, string $payload): string
    {
        $signingString = $timestamp . '.' . $payload;
        return hash_hmac('sha256', $signingString, $this->keySecret);
    }

    /**
     * Build HMAC authentication headers
     */
    private function buildHeaders(?string $payload = null): array
    {
        $timestamp = $this->getTimestamp();
        $payloadStr = $payload ?? '';
        $signature = $this->calculateSignature($timestamp, $payloadStr);

        return [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: KiriMel-PHP-SDK/2.0.0',
            'X-API-Key: ' . $this->apiKey,
            'X-Timestamp: ' . $timestamp,
            'X-Signature: ' . $signature,
        ];
    }

    /**
     * Make HTTP request with retry logic
     */
    private function request(string $method, string $url, ?array $data = null, int $attempt = 0): array
    {
        $this->log('debug', "Making {$method} request to {$url}", ['attempt' => $attempt + 1]);

        $payload = $data !== null ? json_encode($data) : null;
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->buildHeaders($payload),
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            if ($attempt < $this->retries - 1) {
                $this->log('warning', "Network error, retrying...", ['error' => $curlError]);
                usleep(100000 * ($attempt + 1));
                return $this->request($method, $url, $data, $attempt + 1);
            }
            throw new ApiException("Network error: {$curlError}", 0);
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 400) {
            $this->handleError($httpCode, $decoded, $url, $attempt);
        }

        return $decoded;
    }

    /**
     * Handle API errors
     */
    private function handleError(int $httpCode, ?array $response, string $url, int $attempt): void
    {
        $message = $response['message'] ?? 'API request failed';
        $errors = $response['errors'] ?? null;

        switch ($httpCode) {
            case 401:
                throw new AuthenticationException($message, 0, $httpCode, $errors);
            case 429:
                $retryAfter = $response['retry_after'] ?? null;
                if ($retryAfter && $attempt < $this->retries - 1) {
                    $this->log('info', "Rate limited, waiting {$retryAfter}s...");
                    sleep($retryAfter);
                    return;
                }
                throw new RateLimitException($message, 0, $httpCode, $errors, $retryAfter);
            case 422:
                throw new ValidationException($message, 0, $httpCode, $errors);
            default:
                throw new ApiException($message, 0, $httpCode, $errors);
        }
    }

    /**
     * Log message
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger && method_exists($this->logger, $level)) {
            $this->logger->$level($message, $context);
        }
    }
}
