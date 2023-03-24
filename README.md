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
and pick rules you want to use

### Guzzle - ClientCallWithoutOptionRule
Finds all calls of GuzzleHttp\Client methods without some option e.g. timeout, connect_timeout

```neon
services:
    -
        factory: Efabrica\PHPStanRules\Rule\Guzzle\ClientCallWithoutOptionRule(['timeout', 'connect_timeout'])
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
Checks if names of all input parameters. Every name has to contain only alphanumeric characters and `_`
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

### Check calling method in object method
Checks if some method is not used in disabled context - specific method of object.

```neon
parameters:
    disabledMethodCalls:
        -
            context: 'WithCallInterface::checkedMethod'
            disabled: 'ClassWithDisabledMethod::disabledMethod'

services:
    -
        factory: Efabrica\PHPStanRules\Rule\General\DisableMethodCallInContextRule(%disabledMethodCalls%)
        tags:
            - phpstan.rules.rule
```

```php
class ClassWithDisabledMethod implements WithDisabledMethodInterface
{
    public function disabledMethod() {} // this method shouldn't be called in WithCallInterface::checkedMethod
}
```

```php
final class SomeClass implements WithCallInterface
{
    public function checkedMethod(): array
    {
        return [(new ClassWithDisabledMethod)->disabledMethod()]
    }
}
```
:x:

```php
final class SomeClass implements WithCallInterface
{
    public function checkedMethod(): array
    {
        return [(new ClassWithDisabledMethod)]
    }
}
```
:+1:

### Check calling method with required parameters
Checks if some method is called with all required parameters with corresponding types.

```neon
parameters:
    requiredParametersInMethodCalls:
        -
            context: 'SomeClass::someMethod'
            parameters:
                -
                    name: someParameter
                    type: string
                    tip: 'Always use parameter someParameter as string because...'

services:
    -
        factory: Efabrica\PHPStanRules\Rule\General\RequiredParametersInMethodCallRule(%requiredParametersInMethodCalls%)
        tags:
            - phpstan.rules.rule
```

```php
class SomeClass
{
    public function someMethod(?string $someParameter = null): void
    {
        // this method should be called with string value of $someParameter
    }
}
```

```php
class Foo
{
    public function bar(SomeClass $someClass)
    {
        $someClass->someMethod();
    }
}
```
:x:

```php
class Foo
{
    public function bar(SomeClass $someClass)
    {
        $someClass->someMethod('baz');
    }
}
```
:+1:
