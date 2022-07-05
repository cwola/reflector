# reflector

PHP Reflections(Cwola library).

## Overview

Providing simple reflections for PHP.

## Requirement
- PHP8.0+

## Usage
```
<?php

use Cwola\Reflector\ReflectionClass;

class Foo {
    /**
     * @var string
     */
    private string $privateString = 'Private!!';

    /**
     * @param string $text
     *
     * @return string
     */
    private function privateMethod(string $text): string {
        return 'Private: ' . $text;
    }
}

$foo = new Foo;
$reflector = ReflectionClass::make($foo);

// Property
$reflectionPrivateProperty = $reflector->property('privateString')->accessible(true);
echo $reflectionPrivateProperty->get();  // Private!!
$reflectionPrivateProperty->set('Modified');
echo $reflectionPrivateProperty->get();  // Modified

// Method
$reflectionPrivateMethod = $reflector->method('privateMethod')->accessible(true);
echo $reflectionPrivateMethod->call('hijack');  // Private: hijack

// call reflection method
$reflectionPrivateMethod->isConstructor();  // false
```

## Licence

[MIT](https://github.com/cwola/reflector/blob/main/LICENSE)
