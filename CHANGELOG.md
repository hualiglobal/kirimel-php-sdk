# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.1] - 2026-05-10

### Added
- `Customers::lookupByEmail()` method - Look up customers by email address

## [2.0.0] - 2026-05-09

### Added
- Loyalty API support with HMAC SHA256 authentication
- Loyalty resources: Customers, Points, Vouchers, Wallet
- Separate `LoyaltyHttpClient` for loyalty API requests

## [1.0.0] - 2025-01-XX

### Added
- Initial release of KiriMel PHP SDK
- Campaigns resource client
- Subscribers resource client
- Lists resource client
- Segments resource client
- Templates resource client
- Forms resource client
- Conversions resource client
- Landing Pages resource client
- Workflows resource client
- HTTP client with retry logic and exponential backoff
- Exception classes (ApiException, AuthenticationException, RateLimitException, ValidationException)
- PSR-4 autoloading
- PHPUnit tests
