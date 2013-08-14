# StringTemplate

StringTemplate is a very simple string template engine for php. I've written it to have a thing like sprintf, but with named and nested substutions.

[![Build Status](https://secure.travis-ci.org/nicmart/StringTemplate.png?branch=master)](http://travis-ci.org/nicmart/StringTemplate)

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