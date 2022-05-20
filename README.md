# eFabrica PHPStan extension
Extension for PHPStan providing several services and rules to help find bugs in your applications.

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev efabrica/phpstan-rules
```

## Setup

This extension adds several rules. You can use them all by including these files in your `phpstan.neon`:
```neon
includes:
    - vendor/phpstan/phpstan-nette/extension.neon
    - vendor/phpstan/phpstan-nette/rules.neon
```

Or include just `extension.neon` and pick rules you want

### GuzzleClientCallWithoutTimeoutOptionRule
Finds all calls of GuzzleHttp\Client methods without timeout option
```neon
services:
    -
        factory: Efabrica\PHPStanRules\Rule\GuzzleClientCallWithoutTimeoutOptionRule
        tags:
            - phpstan.rules.rule
```

```php
use GuzzleHttp\Client;

$guzzleClient = new Client();
$guzzleClient->request('GET', 'https://example.com/api/url');
```
:x:

```php
use GuzzleHttp\Client;

$guzzleClient = new Client();
$guzzleClient->request('GET', 'https://example.com/api/url', ['timeout' => 3]);
```
:+1:
