Dumper
======

Dumper is a simple and ready to use PHP dumper.

Usage
=========

Install the latest version with `composer require gerardpastor/dumper`

Enabling all dumping functions is as easy as calling `enable()` method on the
main `Dumper` class:

```php
use Deg\Dumper\Dumper;

Dumper::enable(Dumper::BROWSER);
```

The first parameter determines what output to use.


Outputs
-------

By default, Dumper includes 3 outputs: dummy, browser and console.

- **browser**: Dumps variables to browser (uses HTML formatter)
- **console**: Dumps variables to console (uses Console formatter)
- **dummy**: Dumps nothing (used for production environment)


Dumping vars
------------

Dumper defines 3 dumping functions:

`dumpVars`: Dumps each var in $vars
```php
// dumpVars(array $vars, $deep = null)

$vars = array(
    'text',
    123,
    array(),
);

dumpVars($vars, 1);
```

`dump`: Dump $var
```php
// dump($var, $deep = null)

$var = 'foo';

dump($var, 1);
```

`dumpAll`: Dump each argument
```php
// dumpAll($var, $_ = null)

$var1 = 'foo';
$var2 = 'var';

dumpAll($var1, $var2);
```

All of this functions starting with "e" dumps and ends up execution.

```php
edumpVars(array('text', 123, array()), 1);

// Or
edump('foo', 1);

// Or
edumpAll('foo', 'var');
```


Dumping backtrace
-----------------

You can dump current debug backtrace:

```php
// Dumps current debug backtrace
// dumpBacktrace($limit = null)

dumpBacktrace();

// Or
edumpBacktrace();
```


Raw var_dump
-----------------

Aditionally, Dumper provides `rawDump` funtion that does a native `var_dump` inside a `<pre>` tag.

```php
// Dumps vars using native var_dump
// rawDump($var, $_ = null)

rawDump('foo', 'var');
```

Configure Dumper
================

You can configure some default parameters on Dumper.

Accessing Dumper Instance
-------------------------

To configure Dumper you must acces to its instance.
You can access dumper instance when call `enable()` or by calling `getInstance()` when `Dumper` is already enabled.

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable(Dumper::BROWSER, false);
$dumper->dump('foo');

$dumper = Dumper::getInstance();
$dumper->dump('foo');
```

Set default max dumping deep
----------------------------

You can set the default max dumping deep by passing to the `VarParser`:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable(Dumper::BROWSER);
$dumper->getVarParser()->setMaxDeep(3);

// Dumps at 3 levels
dump($arrayOrObject);
```

You can override this value in any call to `dump` or `dumpVars` as the second argument:

```php
// Dumps at 2 levels
dump($arrayOrObject, 2);
```

Limit the number of stack frames in backtrace dumping
-----------------------------------------------------

By default, Dumper dumps all stack frames in backtrace. You can limit this number globally by passing to the `BacktraceFactory`:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable(Dumper::BROWSER);
$dumper->getBacktraceFactory()->setMaxLimit(3);

// Dumps 3 stack frames
dumpBacktrace();
```

You can override this value in any call to `dumpBacktrace` as the first argument:

```php
// Dumps 2 stack frames
dumpBacktrace(2);
```

Add excludes to backtrace
-------------------------

Dumper exculdes all namespaces and directories from Dumper, but you can add your own by passing to the `BacktraceFactory`:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable(Dumper::BROWSER);
$dumper->getBacktraceFactory()->addExcule('Foo/Var');

//Or
$dumper->getBacktraceFactory()->addExcule(__DIRECTORY__ . '/foo/var');
```


Disabling global functions
==========================

You can disable the definition of Dumper as a global functions by passing `false` as the second argument when calling `enable()`.
Then, you can still access dumper functions by calling directly on a dumper instance:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable(Dumper::BROWSER, false);

$dumper->dump('foo');
```

You can then enable this global functions at any time by calling `defineGlobalFunctions()`:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable(Dumper::BROWSER, false);
Dumper::defineGlobalFunctions();

dump('foo');
```

Var Tokenizers
==============

Dumper uses tokenizers to convert any variable into a string.
A tokenizer receives a variable and returns a `TokenStream` (that is a collection of `Token`)

Dumper provides tokenizer to parse the most generic variable types:
- _ObjectTokenizer_: Parses an object
- _ArrayTokenizer_: Parses an array
- _StringTokenizer_: Parses an string
- _GenericTokenizer_: Parses any variable (in a very simple way)

Tokenizer has an `accept($var)` and a `getConfidence()` methods.
The parser will use the tokenizer with bigger confidence from those that accepted the given variable.


Custom Tokenizers
-----------------

You can add more specific or sophisticated parsing by adding custom tokenizers.
To do that, you must create a class that implements `TokenizerInterface` and pass to the `VarParser`:

```php
namespace Foo\Var;

use Deg\Dumper\Parser\VarParser;
use Deg\Dumper\Parser\TokenStream;
use Deg\Dumper\Parser\TokenStreamBuilder;

class MyCustomTokenizer implements TokenizerInterface
{
    public function tokenize($var, $deep, VarParser $parser)
    {
        $type = gettype($var);

        $builder = new TokenStreamBuilder();

        // Build the stream using a TokenStreamBuilder
        $builder
            ->addKeyword($type)
            ->addBrace('(')
            ->addNumber($var)
            ->addBrace(')')
        ;

        // Tokenize must return a TokenStream
        return $builder->getStream();
    }

    public function accept($var)
    {
        // Tells if this tokenizer can tokenize the given variable
        return is_number($var);
    }

    public function getConfidence()
    {
        // How specific is this tokenizer (bigger number means more specific)
        return 20;
    }

}
```

And then, pass to `VarParser`:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable(Dumper::BROWSER);
$dumper->getVarParser()->addTokenizer(new Foo\Var\MyCustomTokenizer());
```

Take a look at provided tokenizers for more specific examples.


Using Dumper as Object
======================

The `enable()` method simply loads a default configuration, but you can
instantiate Dumper manually, without the use of `enable()`:

```php
use Deg\Dumper\Dumper;
use Deg\Dumper\Backtrace\Backtrace;
use Deg\Dumper\Parser\VarParser;
use Deg\Dumper\Parser\BacktraceParser;
use Deg\Dumper\Output\BrowserOutput;

$varParser = new VarParser();
$varParser->addTokenizer(new Tokenizer\GenericTokenizer());

$backtraceParser = new BacktraceParser();
$backtraceFactory = new Backtrace();
$output = new BrowserOutput();

$dumper = new Dumper($varParser, $backtraceParser, $backtraceFactory, $output);

$dumper->dump('foo');
```

If you want the global functions uses your own instance, call `setInstance()`:


```php

// ...

$dumper = new Dumper($varParser, $backtraceParser, $backtraceFactory, $output);

Dumper::setInstance($dumper);
Dumper::defineGlobalFunctions();
```