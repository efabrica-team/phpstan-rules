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
    - vendor/symplify/astral/config/services.neon
    - vendor/phpstan/phpstan-nette/extension.neon
    - vendor/phpstan/phpstan-nette/rules.neon
```

Or include just:
```neon
includes:
    - vendor/symplify/astral/config/services.neon
    - vendor/phpstan/phpstan-nette/extension.neon
```
and pick rules you want

### Guzzle - ClientCallWithoutTimeoutOptionRule
Finds all calls of GuzzleHttp\Client methods without timeout option
```neon
services:
    -
        factory: Efabrica\PHPStanRules\Rule\Guzzle\ClientCallWithoutTimeoutOptionRule
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

### Tomaj/Nette API - InputParamNameRule
Checks names of all input parameters. Every name has to contain only alphanumeric characters and `_`
```neon
services:  
    -
        factory: Efabrica\PHPStanRules\Rule\Tomaj\NetteApi\InputParamNameRule
        tags:
            - phpstan.rules.rule
```

```php
use Tomaj\NetteApi\Handlers\BaseHandler;

final class SomeHandler extends BaseHandler
{
    public function params(): array
    {
        return [
            new GetInputParam('my-name')
        ];
    }
}
```
:x:

```php
use Tomaj\NetteApi\Handlers\BaseHandler;

final class SomeHandler extends BaseHandler
{
    public function params(): array
    {
        return [
            new GetInputParam('my_name')
        ];
    }
}
```
:+1:
