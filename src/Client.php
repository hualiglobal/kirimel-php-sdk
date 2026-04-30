<?php

declare(strict_types=1);

namespace KiriMel;

/**
 * KiriMel API Client
 *
 * @package KiriMel
 */
class Client
{
    private HttpClient $httpClient;
    private array $resourceClients = [];

    // Resource clients
    private ?Resources\Campaigns $campaigns = null;
    private ?Resources\Subscribers $subscribers = null;
    private ?Resources\Lists $lists = null;
    private ?Resources\Segments $segments = null;
    private ?Resources\Templates $templates = null;
    private ?Resources\Forms $forms = null;
    private ?Resources\Conversions $conversions = null;
    private ?Resources\LandingPages $landingPages = null;
    private ?Resources\Workflows $workflows = null;
    private ?Resources\Webhooks $webhooks = null;
    private ?Resources\Email $email = null;

    /**
     * Create a new API client
     *
     * @param array $config Configuration options
     *   - api_key: API key (or use KIRIMEL_API_KEY env var)
     *   - base_url: Base URL (default: https://kirimel.com/api)
     *   - timeout: Request timeout in seconds (default: 30)
     *   - retries: Number of retries (default: 3)
     *   - logger: PSR-3 logger instance
     */
    public function __construct(array $config = [])
    {
        $this->httpClient = new HttpClient($config);
    }

    /**
     * Get the HTTP client
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Set a PSR-3 logger
     */
    public function setLogger(object $logger): self
    {
        $this->httpClient->setLogger($logger);
        return $this;
    }

    /**
     * Get campaigns resource client
     */
    public function campaigns(): Resources\Campaigns
    {
        if ($this->campaigns === null) {
            $this->campaigns = new Resources\Campaigns($this->httpClient);
        }
        return $this->campaigns;
    }

    /**
     * Get subscribers resource client
     */
    public function subscribers(): Resources\Subscribers
    {
        if ($this->subscribers === null) {
            $this->subscribers = new Resources\Subscribers($this->httpClient);
        }
        return $this->subscribers;
    }

    /**
     * Get lists resource client
     */
    public function lists(): Resources\Lists
    {
        if ($this->lists === null) {
            $this->lists = new Resources\Lists($this->httpClient);
        }
        return $this->lists;
    }

    /**
     * Get segments resource client
     */
    public function segments(): Resources\Segments
    {
        if ($this->segments === null) {
            $this->segments = new Resources\Segments($this->httpClient);
        }
        return $this->segments;
    }

    /**
     * Get templates resource client
     */
    public function templates(): Resources\Templates
    {
        if ($this->templates === null) {
            $this->templates = new Resources\Templates($this->httpClient);
        }
        return $this->templates;
    }

    /**
     * Get forms resource client
     */
    public function forms(): Resources\Forms
    {
        if ($this->forms === null) {
            $this->forms = new Resources\Forms($this->httpClient);
        }
        return $this->forms;
    }

    /**
     * Get conversions resource client
     */
    public function conversions(): Resources\Conversions
    {
        if ($this->conversions === null) {
            $this->conversions = new Resources\Conversions($this->httpClient);
        }
        return $this->conversions;
    }

    /**
     * Get landing pages resource client
     */
    public function landingPages(): Resources\LandingPages
    {
        if ($this->landingPages === null) {
            $this->landingPages = new Resources\LandingPages($this->httpClient);
        }
        return $this->landingPages;
    }

    /**
     * Get workflows resource client
     */
    public function workflows(): Resources\Workflows
    {
        if ($this->workflows === null) {
            $this->workflows = new Resources\Workflows($this->httpClient);
        }
        return $this->workflows;
    }

    /**
     * Get webhooks resource client
     */
    public function webhooks(): Resources\Webhooks
    {
        if ($this->webhooks === null) {
            $this->webhooks = new Resources\Webhooks($this->httpClient);
        }
        return $this->webhooks;
    }

    /**
     * Get email resource client for transactional emails
     */
    public function email(): Resources\Email
    {
        if ($this->email === null) {
            $this->email = new Resources\Email($this->httpClient);
        }
        return $this->email;
    }

    /**
     * Magic property getter for resource clients
     *
     * @param string $name Resource name
     * @return mixed Resource client
     */
    public function __get(string $name)
    {
        $method = $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \InvalidArgumentException("Unknown resource: {$name}");
    }
}
