# eFabrica PHPStan extension
Extension for PHPStan is providing several services and rules to help find bugs in your applications.

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev efabrica/phpstan-rules
```

## Setup

This extension adds several rules. You can use them all by including these files in your `phpstan.neon`:
```neon
includes:
    - vendor/efabrica/phpstan-rules/extension.neon
    - vendor/efabrica/phpstan-rules/rules.neon
```

Or include just:
```neon
includes:
    - vendor/efabrica/phpstan-rules/extension.neon
```
and pick rules you want

### Guzzle - ClientCallWithoutOptionRule
Finds all calls of GuzzleHttp\Client methods without some option e.g. timeout, connect_timeout

```neon
services:
    guzzleClientCallWithoutTimeoutOptionRule:
        factory: Efabrica\PHPStanRules\Rule\Guzzle\ClientCallWithoutOptionRule('timeout')
        tags:
            - phpstan.rules.rule

    guzzleClientCallWithoutConnectTimeoutOptionRule:
        factory: Efabrica\PHPStanRules\Rule\Guzzle\ClientCallWithoutOptionRule('connect_timeout')
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
$guzzleClient->request('GET', 'https://example.com/api/url', ['timeout' => 3, 'connect_timeout' => 1]);
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

### Check trait context - TraitContextRule
Checks if traits are used only in context of classes specified in them via comment `@context {Type}`
```neon
services:  
    -
        factory: Efabrica\PHPStanRules\Rule\General\TraitContextRule
        tags:
            - phpstan.rules.rule
```

```php
/**
 * @context MyInterface
 */
trait MyTrait
{

}

final class SomeClass
{
    use MyTrait;
}
```
:x:

```php
/**
 * @context MyInterface
 */
trait MyTrait
{

}

final class SomeClass implements MyInterface
{
    use MyTrait;
}
```
:+1:
