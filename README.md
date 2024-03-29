# eFabrica PHPStan extension

Extension for PHPStan is providing several services and rules to help find bugs in your applications.

[![PHP unit](https://github.com/efabrica-team/phpstan-rules/workflows/PHPunit/badge.svg)](https://github.com/efabrica-team/phpstan-rules/actions?query=workflow%3APHPunit)
[![PHPStan level](https://img.shields.io/badge/PHPStan-level:%20max-brightgreen.svg)](https://github.com/efabrica-team/phpstan-rules/actions?query=workflow%3A"PHP+static+analysis")
[![PHP static analysis](https://github.com/efabrica-team/phpstan-rules/workflows/PHP%20static%20analysis/badge.svg)](https://github.com/efabrica-team/phpstan-rules/actions?query=workflow%3A"PHP+static+analysis")
[![Latest Stable Version](https://img.shields.io/packagist/v/efabrica/phpstan-rules.svg)](https://packagist.org/packages/efabrica/phpstan-rules)
[![Total Downloads](https://img.shields.io/packagist/dt/efabrica/phpstan-rules.svg?style=flat-square)](https://packagist.org/packages/efabrica/phpstan-rules)

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

### Do not concatenate translated strings
Every language has its own word order in sentences, we can't use e.g. variables at the same place for all languages. There are mechanisms in translate libraries e.g. symfony/translator - we can use placeholders like %name% etc.
This rule checks if you use translated messages and then concat them with some other strings.

```neon
parameters:
    translateCalls:
        - iAmTranslateFunction
        - Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Source\TranslatorInterface::iAmTranslateMethod
        - Efabrica\PHPStanRules\Tests\Rule\General\DisabledConcatenationWithTranslatedStringsRule\Source\TranslatorInterface::iAmTranslateStaticMethod
    allowedTranslateConcatenationPatterns:
        - '[\s]*<.*?>[\s]*<\/.*?>[\s]*'
        - '[\s]*This is allowed text[\s]*'
        - '[\s]*\#[0-9]+[\s]*'

services:
    -
        factory: Efabrica\PHPStanRules\Rule\General\DisabledConcatenationWithTranslatedStringsRule(%translateCalls%)
        tags:
            - phpstan.rules.rule
```

```php
$message = 'Hello';
$name = 'Mark';
echo $translator->iAmTranslateMethod($message) . ' ' . $name;
```

:x:

```php
$message = 'Hello %name%';
$name = 'Mark';
echo $translator->iAmTranslateMethod($message, ['name' => $name];
```
:+1:

### Forbidden constructor parameters types
This rule checks if constructor contains forbidden parameter types.

```neon
parameters:
    forbiddenConstructorParametersTypes:
        -
            context: 'SomeClass'
            forbiddenTypes:
                -
                    type: ForbiddenType
                    tip: 'ForbiddenType is not allowed, use NotForbiddenType instead'

services:
    -
        factory: Efabrica\PHPStanRules\Rule\General\ForbiddenConstructorParametersTypesRule(%forbiddenConstructorParametersTypes%)
        tags:
            - phpstan.rules.rule
```

```php
class SomeClass
{

}

class ForbiddenType
{

}

class NotForbiddenType
{

}
```

```php
class Foo extends SomeClass
{
    public function __construct(ForbiddenType $type)
    {
    
    }
}
```
:x:

```php
class Foo extends SomeClass
{
    public function __construct(NotForbiddenType $type)
    {
    
    }
}
```
:+1:

### Performance - DisabledCallsInLoopsRule
Some functions are not recommended to be called in loops. For example array_merge.

```neon
services:
    -
        factory: Efabrica\PHPStanRules\Rule\Performance\DisabledCallsInLoopsRule
        tags:
            - phpstan.rules.rule
```

```php
$result = [];
for ($i = 0; $i < 100; $i++) {
    $result = array_merge($result, $data[$i]);
}

```
:x:

```php
$result = array_merge([], ...$data);
```
:+1:
