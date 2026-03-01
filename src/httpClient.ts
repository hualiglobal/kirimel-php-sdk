/**
 * HTTP Client for KiriMel API
 */
import fetch, { Response } from 'node-fetch';
import {
  ApiException,
  AuthenticationException,
  RateLimitException,
  ValidationException,
} from './exceptions';

export interface HttpClientConfig {
  apiKey?: string;
  baseUrl?: string;
  timeout?: number;
  retries?: number;
  logger?: Logger;
}

interface RequestOptions {
  method: string;
  headers: Record<string, string>;
  body?: string;
  timeout?: number;
}

export interface Logger {
  debug(message: string, context?: any): void;
  info(message: string, context?: any): void;
  warn(message: string, context?: any): void;
  error(message: string, context?: any): void;
}

export class HttpClient {
  private readonly baseUrl: string;
  private readonly apiKey: string | undefined;
  private readonly timeout: number;
  private readonly retries: number;
  private logger?: Logger;

  constructor(config: HttpClientConfig = {}) {
    this.baseUrl = (config.baseUrl || 'https://kirimel.com').replace(/\/$/, '');
    this.apiKey = config.apiKey || process.env.KIRIMEL_API_KEY;
    this.timeout = config.timeout || 30000;
    this.retries = config.retries || 3;
    this.logger = config.logger;
  }

  public setLogger(logger: Logger): void {
    this.logger = logger;
  }

  public async get(path: string, params?: Record<string, any>): Promise<any> {
    const url = this.buildUrl(path, params);
    return this.request('GET', url);
  }

  public async post(path: string, data?: Record<string, any>): Promise<any> {
    return this.request('POST', this.buildUrl(path), data);
  }

  public async put(path: string, data?: Record<string, any>): Promise<any> {
    return this.request('PUT', this.buildUrl(path), data);
  }

  public async delete(path: string): Promise<any> {
    return this.request('DELETE', this.buildUrl(path));
  }

  private buildUrl(path: string, params?: Record<string, any>): string {
    const cleanPath = path.replace(/^\//, '');
    let url = `${this.baseUrl}/${cleanPath}`;

    if (params && Object.keys(params).length > 0) {
      const searchParams = new URLSearchParams();
      for (const [key, value] of Object.entries(params)) {
        if (value !== undefined && value !== null) {
          searchParams.append(key, String(value));
        }
      }
      url += `?${searchParams.toString()}`;
    }

    return url;
  }

  private async request(
    method: string,
    url: string,
    data?: Record<string, any>,
    attempt = 0
  ): Promise<any> {
    this.log('debug', `Making ${method} request to ${url}`, { attempt: attempt + 1 });

    const headers = this.buildHeaders();
    const options: RequestOptions = {
      method,
      headers,
      timeout: this.timeout,
    };

    if (data) {
      options.body = JSON.stringify(data);
    }

    try {
      const response: Response = await fetch(url, options as any);

      if (response.status >= 400) {
        await this.handleError(response, attempt);
      }

      return await response.json();
    } catch (error: any) {
      // Network error - retry if attempts remain
      if (attempt < this.retries - 1 && !error.statusCode) {
        this.log('warn', 'Network error, retrying...', { error: error.message });
        await this.sleep(100 * (attempt + 1)); // Exponential backoff
        return this.request(method, url, data, attempt + 1);
      }
      throw new ApiException(`Network error: ${error.message}`);
    }
  }

  private buildHeaders(): Record<string, string> {
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'User-Agent': 'KiriMel-Node-SDK/0.1.0',
    };

    if (this.apiKey) {
      headers['Authorization'] = `Bearer ${this.apiKey}`;
    }

    return headers;
  }

  private async handleError(response: Response, attempt: number): Promise<void> {
    let message = 'API request failed';
    let errors: any;
    let retryAfter: number | undefined;

    try {
      const data: any = await response.json();
      message = data.message || message;
      errors = data.errors;
      retryAfter = data.retry_after;
    } catch {
      // Use default error message
    }

    if (response.status === 401) {
      throw new AuthenticationException(message, response.status, errors);
    }

    if (response.status === 429) {
      if (retryAfter && attempt < this.retries - 1) {
        this.log('info', `Rate limited, waiting ${retryAfter}s...`);
        await this.sleep(retryAfter * 1000);
        return; // Will retry
      }
      throw new RateLimitException(message, response.status, errors, retryAfter);
    }

    if (response.status === 422) {
      throw new ValidationException(message, response.status, errors);
    }

    throw new ApiException(message, response.status, errors);
  }

  private sleep(ms: number): Promise<void> {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  private log(level: string, message: string, context?: any): void {
    if (this.logger) {
      const logger = this.logger as any;
      if (typeof logger[level] === 'function') {
        logger[level](message, context);
      }
    }
  }
}
