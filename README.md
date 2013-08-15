# StringTemplate

StringTemplate is a very simple string template engine for php. 

I've written it to have a thing like sprintf, but with named and nested substitutions.

For installing instructions, go to the end of this README.

[![Build Status](https://travis-ci.org/nicmart/StringTemplate.png?branch=master)](https://travis-ci.org/nicmart/StringTemplate)

## Why

I have often struggled against sprintf's lack of a named placeholders feature, 
so I have decided to write once and for all a simple component that allows you to render a template string in which
placeholders are named.

Furhtermore, its placeholders can be nested as much as you want (multidimensional arrays allowed).

## Usage
Simply create an instance of `StringTemplate\Engine`, and use its `render` method. 

Placeholders are delimited by default by `{` and `}`, but you can specify others through
the class constructor.

```php
$engine = new StringTemplate\Engine;

//Scalar value: returns "This is my value: nic"
$engine->render("This is my value: {}", 'nic');

```

You can also provide an array value:

```php
//Array value: returns "My name is Nicolò Martini"
$engine->render("My name is {name} {surname}", ['name' => 'Nicolò', 'surname' => 'Martini']);

```

Nested array values are allowed too! Example:

```php
//Nested array value: returns "My name is Nicolò and her name is Gabriella"
$engine->render(
    "My name is {me.name} and her name is {her.name}",
    [
        'me' => ['name' => 'Nicolò'],
        'her' => ['name' => 'Gabriella']
    ]);

```

You can change the delimiters as you want:
```php
$engine = new StringTemplate\Engine(':', '');

//Returns I am Nicolò Martini
$engine->render(
    "I am :name :surname",
    [
        'name' => 'Nicolò',
        'surname' => 'Martini'
    ]);

```

## NestedKeyIterator
Internally the engine iterates through the value array with the `NestedKeyIterator`. `NestedKeyIterator`
iterates through multi-dimensional arrays giving as key the imploded keys stack.

It can be useful even if you don't need the Engine. Keep in mind that it is an `RecursiveIteratorIterator`,
and so you have to pass  a `RecursiveIterator` to its constructor.

Example:
```php
use StringTemplate\NestedKeyIterator

$ary = [
    '1' => 'foo',
    '2' => [
        '1' => 'bar',
        '2' => ['1' => 'fog']
    ],
    '3' => [1, 2, 3]
];

$iterator = new NestedKeyIterator(new RecursiveArrayIterator($ary));

foreach ($iterator as $key => $value)
    echo "$key: $value\n";

// Prints
// 1: foo
// 2.1: bar
// 2.2.1: fog
// 3.0: 1
// 3.1: 2
// 3.2: 3

```
## Where is it used
I use StringTemplate in [DomainSpecificQuery](https://github.com/comperio/DomainSpecificQuery) 
to implement the `Lucene\TemplateExpression` class.

## Install

The best way to install StringTemplate is [through composer](http://getcomposer.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "nicmart/string-template": "dev-master"
    }
}
```

Then you can run these two commands to install it:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

or simply run `composer install` if you have have already [installed the composer globally](http://getcomposer.org/doc/00-intro.md#globally).

Then you can include the autoloader, and you will have access to the library classes:

```php
<?php
require 'vendor/autoload.php';
```
