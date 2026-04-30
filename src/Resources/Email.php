<?php

declare(strict_types=1);

namespace KiriMel\Resources;

use KiriMel\HttpClient;

/**
 * Email resource client for transactional emails
 */
class Email
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Send transactional email
     *
     * @param array $data Email data
     *   - to: string|array - Recipient email(s)
     *   - subject: string - Email subject
     *   - html: string - HTML content (optional)
     *   - text: string - Plain text content (optional)
     *   - from_name: string - From name (optional)
     *   - reply_to: string - Reply-to address (optional)
     *   - cc: string|array - CC recipients (optional)
     *   - bcc: string|array - BCC recipients (optional)
     *   - attachments: array - Attachments array (optional)
     *       - name: string - File name
     *       - content: string - Base64 encoded content
     * @return array Response with message_id and tracking_id
     */
    public function send(array $data): array
    {
        return $this->httpClient->post('email/send', $data);
    }

    /**
     * Get SES send quota
     *
     * @return array Quota information
     *   - max_24_hour_send: int
     *   - sent_last_24_hours: int
     *   - remaining: int
     *   - max_send_rate_per_second: float
     *   - utilization_percent: float
     */
    public function quota(): array
    {
        return $this->httpClient->get('email/quota');
    }

    /**
     * Get verified emails
     *
     * @return array List of verified email addresses
     */
    public function verifiedEmails(): array
    {
        return $this->httpClient->get('email/verified');
    }

    /**
     * Verify email address
     *
     * @param string $email Email address to verify
     * @return array Verification status
     */
    public function verifyEmail(string $email): array
    {
        return $this->httpClient->post('email/verify', ['email' => $email]);
    }
}
