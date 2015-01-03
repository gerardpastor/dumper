Dumper
======

Dumper is a simple and ready to use PHP dumper.

Enabling all dumping functions is as easy as calling `enable()` method on the
main `Dumper` class:

```php
use Deg\Dumper\Dumper;

Dumper::enable('browser');
```

The first parameter determines what output to use.


Outputs
-------

By default, Dumper includes 3 outputs: dummy, browser and console.

- **BROWSER**: Dumps variables to browser (uses HTML formatter)
- **CONSOLE**: Dumps variables to console (uses Console formatter)
- **DUMMY**: Dumps nothing (used for production environment)


Functions
=========

Dumping vars
------------

By default, Dumper defines 3 dumper functions:


```php
// Dumps each var in $vars (accepts
// dumpVars(array $vars, $deep = null)

$vars = array(
    'text',
    123,
    array(),
);

dumpVars($vars, 1);
```

```php
// Dump $var
// dump($var, $deep = null)

$var = 'foo';

dump($var, 1);
```

```php
// Dump each arguments
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

Aditionally, Dumper provides `rawDump` funtion that does a native var_dump inside a `<pre>` tag.

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

You can access dumper instance when call `enable()` or by calling `getInstance()` when Dumper es already enabled

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable('browser', false);
$dumper->dump('foo');

$dumper = Dumper::getInstance();
$dumper->dump('foo');
```

Set default max dumping deep
----------------------------

You can set the default max dumping deep by calling `setDefaultMaxDeep` on a Dumper instance:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable('browser');
$dumper->getVarParser()->setMaxDeep(3);

// Dumps at 3 levels
dump($arrayOrObject);
```

You can override this value in any call to `dump` or `dumpVars` as the second argument:

```php
// Dumps at 2 levels
dump($arrayOrObject, 2);
```

Limit the number of stack frames in backtrace
---------------------------------------------

By default, Dumper dumps all stack frames in bactrace. You can limit this number globally by passing to the BacktraceFactory:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable('browser');
$dumper->getBacktraceFactory()->setMaxLimit(3);

// Dumps 3 levels
dumpBacktrace();
```

You can override this value in any call to `dumpBacktrace` as the first argument:

```php
// Dumps 2 levels
dumpBacktrace(2);
```

Add excludes to backtrace
-------------------------

Dumper exculdes all namespaces and directories from Dumper, but you can add your own by passing to the BacktraceFactory:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable('browser');
$dumper->getBacktraceFactory()->addExcule('Foo/Var');

//Or
$dumper->getBacktraceFactory()->addExcule(__DIRECTORY__ . '/foo/var');
```


Disabling global functions
==========================

You can disable the definition of Dumper as a global functions by passing `false` as second argument when calling `enable()`.
Then, you can still access dumper functions by calling directly on a dumper instance:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable('browser', false);

$dumper->dump('foo');
```


Var Tokenizers
==============

Dumper uses tokenizers to convert any variable into a string.
A tokenizer receives a variable and returns a TokenStream (that is a collection of Tokens)

Dumper includes most commonly used Tokenizers:
- **ObjectTokenizer**: Parses an object
- **ArrayTokenizer**: Parses an array
- **StringTokenizer**: Parses an string
- **GenericTokenizer**: Parses any variable (very simple)

Tokenizer has an `accept($var)` and a `getConfidence()` methods.
The parser will use the tokenizer that accepts the given var with bigger confidence.


Custom Tokenizers
-----------------

You can add more specific parsing by adding custom tokenizers.
To do that, you must create a class that implements Tokenizer interface and pass to Dumper:

```php
namespace Foo\Var;

use Deg\Dumper\Parser\VarParser;
use Deg\Dumper\Parser\TokenStream;
use Deg\Dumper\Parser\TokenStreamBuilder;

class MyCustomTokenizer implements TokenizerInterface
{
    /**
     *
     * @param mixed $var
     * @param integer $deep
     * @param VarParser $parser
     * @return TokenStream
     */
    public function tokenize($var, $deep, VarParser $parser)
    {
        $type = gettype($var);

        $builder = new TokenStreamBuilder();

        $builder
            ->addKeyword($type)
            ->addBrace('(')
            ->addNumber($var)
            ->addBrace(')')
        ;

        return $builder->getStream();
    }

    /**
     *
     * @param mixed $var
     * @return boolean
     */
    public function accept($var)
    {
        // Tells if this tokenizer can tokenize the given variable
        return is_number($var);
    }

    /**
     *
     * @return int
     */
    public function getConfidence()
    {
        // How specific is this tokenizer (bigger number means more specific)
        return 20;
    }

}
```

And then, pass to Dumper:

```php
use Deg\Dumper\Dumper;

$dumper = Dumper::enable('browser');
$dumper->getVarParser()->addTokenizer(new Foo\Var\MyCustomTokenizer());
```

Take a look to provided tokenizers for more specific examples.


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