# Package "ctw/ctw-middleware-generatedby"

[![Latest Stable Version](https://poser.pugx.org/ctw/ctw-middleware-generatedby/v/stable)](https://packagist.org/packages/ctw/ctw-middleware-generatedby)
[![GitHub Actions](https://github.com/jonathanmaron/ctw-middleware-generatedby/actions/workflows/tests.yml/badge.svg)](https://github.com/jonathanmaron/ctw-middleware-generatedby/actions/workflows/tests.yml)
[![Scrutinizer Build](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/build-status/master)
[![Scrutinizer Quality](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedby/?branch=master)

PSR-15 middleware that adds an `X-Generated-By` header containing a UUID v5 identifier derived from server IP and hostname, enabling anonymous server identification in load-balanced environments.

## Introduction

### Why This Library Exists

In multi-server deployments behind load balancers, identifying which application server processed a request is essential for debugging and monitoring. However, exposing server IP addresses in response headers creates security risks and violates infrastructure privacy.

This middleware generates a deterministic UUID v5 from the server's IP address (`SERVER_ADDR`) and hostname (`SERVER_NAME`):

- **Anonymous identification**: Each server gets a unique, consistent UUID without exposing its IP
- **Debugging capability**: Quickly identify which server in a cluster handled a specific request
- **Security preservation**: UUIDs cannot be reverse-engineered to reveal server addresses
- **Consistent tracking**: The same server always produces the same UUID

### Problems This Library Solves

1. **Blind load balancing**: Without server identification, debugging issues in clustered environments is difficult
2. **IP address exposure**: Traditional `X-Served-By` headers often expose internal IPs, creating security risks
3. **Inconsistent identification**: Random request IDs don't help identify servers across multiple requests
4. **Infrastructure leakage**: Server hostnames and IPs can reveal infrastructure topology to attackers
5. **Log correlation**: Matching application logs to specific servers without a stable identifier is challenging

### Where to Use This Library

- **Load-balanced deployments**: Identify which backend server handled each request
- **Kubernetes/container environments**: Track requests to specific pods without exposing pod IPs
- **Multi-region deployments**: Distinguish between geographic server locations
- **Debugging sessions**: Correlate client-side errors with specific server instances
- **Performance analysis**: Identify servers with different performance characteristics

### Design Goals

1. **Privacy-preserving**: Uses UUID v5 hashing to hide actual server details
2. **Deterministic**: Same server always produces the same UUID across restarts
3. **Standards-based**: Uses RFC 4122 UUID v5 (SHA-1 namespace-based)
4. **Minimal overhead**: UUID generation is computationally trivial
5. **Non-intrusive**: Adds metadata header without modifying response content

## Requirements

- PHP 8.3 or higher
- ctw/ctw-middleware ^4.0
- ramsey/uuid ^4.1

## Installation

Install by adding the package as a [Composer](https://getcomposer.org) requirement:

```bash
composer require ctw/ctw-middleware-generatedby
```

## Usage Examples

### Basic Pipeline Registration (Mezzio)

```php
use Ctw\Middleware\GeneratedByMiddleware\GeneratedByMiddleware;

// In config/pipeline.php or similar
$app->pipe(GeneratedByMiddleware::class);
```

### Response Header Output

```http
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
X-Generated-By: 78ac0e14-0f2b-529e-81e2-a0f50f6029c5
```

### Inspecting with cURL

```bash
curl -I https://example.com/

# Response includes:
# X-Generated-By: 78ac0e14-0f2b-529e-81e2-a0f50f6029c5
```

### ConfigProvider Registration

The package includes a `ConfigProvider` for automatic factory registration:

```php
// config/config.php
return [
    // ...
    \Ctw\Middleware\GeneratedByMiddleware\ConfigProvider::class,
];
```

### UUID Generation

The UUID v5 is generated from a combination of server parameters:

| Parameter | Source | Example |
|-----------|--------|---------|
| Server IP | `$_SERVER['SERVER_ADDR']` | `192.168.1.100` |
| Server Name | `$_SERVER['SERVER_NAME']` | `www.example.com` |
| Combined | Concatenated, lowercased | `192.168.1.100www.example.com` |
| UUID v5 | SHA-1 hash with URL namespace | `78ac0e14-0f2b-529e-81e2-a0f50f6029c5` |

### Server Identification Table

Create a mapping table for your infrastructure:

| Server | IP | Hostname | UUID |
|--------|------|----------|------|
| web-01 | 10.0.1.10 | app.example.com | `a1b2c3d4-...` |
| web-02 | 10.0.1.11 | app.example.com | `e5f6g7h8-...` |
| web-03 | 10.0.1.12 | app.example.com | `i9j0k1l2-...` |

This allows you to identify servers from response headers without exposing infrastructure details.
