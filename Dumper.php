<?php

/*
 * This file is part of the Deg package.
 *
 * (c) Gerard Pastor <gerardpastor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deg\Dumper;

use Deg\Dumper\Backtrace\Backtrace;
use Deg\Dumper\Parser\VarParser;
use Deg\Dumper\Parser\BacktraceParser;
use Deg\Dumper\Output\OutputInterface;
use Deg\Dumper\Parser\TokenStream;
use Deg\Dumper\Parser\Tokenizer;
use Deg\Dumper\Parser\Tokenizer\TokenizerInterface;
use Deg\Dumper\Formatter;
use Deg\Dumper\Output;

/**
 * Simple PHP var dumper.
 *
 * @author Gerard Pastor <gerardpastor@gmail.com>
 *
 * This code is distributed under MIT License.
 */
class Dumper
{
    /**
     *
     * @var VarParser
     */
    private $varParser;

    /**
     *
     * @var BacktraceParser
     */
    private $backtraceParser;

    /**
     *
     * @var Backtrace
     */
    private $backtraceFactory;

    /**
     *
     * @var OutputInterface
     */
    private $outputs;

    /**
     *
     * @var clousure
     */
    private $endClousure;

    /**
     *
     * @var string
     */
    private $defaultOutput;

    public function __construct(VarParser $varParser, BacktraceParser $backtraceParser, Backtrace $backtraceFactory, $endClousure = null)
    {
        $this->varParser = $varParser;
        $this->backtraceParser = $backtraceParser;
        $this->backtraceFactory = $backtraceFactory;

        $this->outputs['dummy'] = new Output\NullOutput();
        $this->defaultOutput = 'dummy';

        $this->endClousure = $endClousure ? : function () {
            die();
        };
    }

    /**
     *
     * @param string $key
     * @return OutputInterface
     * @throws \InvalidArgumentException
     */
    public function getOutput($key)
    {
        if (!array_key_exists($key, $this->outputs)) {
            throw new \InvalidArgumentException(sprintf('Output "%s" not exists', $key));
        }

        return $this->outputs[$key];
    }

    /**
     *
     * @return array|OutputInterface[]
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     *
     * @param OutputInterface $output
     */
    public function addOutput($key, OutputInterface $output)
    {
        $this->outputs[$key] = $output;
    }

    /**
     *
     * @param TokenizerInterface $tokenizer
     */
    public function addTokenizer(TokenizerInterface $tokenizer)
    {
        $this->varParser->addTokenizer($tokenizer);
    }

    /**
     *
     * @return array|TokenizerInterface[]
     */
    public function getTokenizers()
    {
        return $this->varParser->getTokenizers();
    }

    /**
     *
     * @return int
     */
    public function getDefaultMaxDeep()
    {
        return $this->varParser->getMaxDeep();
    }

    /**
     *
     * @param int $maxDeep
     */
    public function setDefaultMaxDeep($maxDeep)
    {
        $this->varParser->setMaxDeep($maxDeep);
    }

    /**
     *
     * @param string $defaultOutput
     */
    public function setDefaultOutput($defaultOutput)
    {
        $this->defaultOutput = $defaultOutput;
    }

    // Shortcuts
    /**
     * Gets backtrace
     *
     * @param  int   $limit Max traces to get
     * @return array
     */
    public function getBacktrace($limit = null)
    {
        return $this->backtraceFactory->get($limit);
    }

    /**
     * Gets caller
     *
     * @return array|null
     */
    public function getCaller()
    {
        $backtrace = $this->backtraceFactory->get();

        return count($backtrace) ? $backtrace[0] : null;
    }

    // Dumb functions
    /**
     * Dumps all array values
     *
     * @param array  $var     Array of vars to dump
     * @param int    $deep    Deep of dumpping
     * @param string $output  Output
     */
    public function dumpVars(array $vars, $deep = null, $output = null)
    {
        $messages = array();
        foreach ($vars as $var) {
            $messages[] = $this->varParser->parse($var, $deep);
        }

        $this->write($messages, $output);
    }

    /**
     * Dumps information about a variable
     *
     * @param type   $var     Variable to dump
     * @param int    $deep    Deep of dumpping
     * @param string $output  Output
     */
    public function dump($var, $deep = null, $output = null)
    {
        $this->dumpVars(array($var), $deep, $output);
    }

    /**
     * Dumps all arguments
     *
     * @param mixed $var Variable to dump
     * @param mixed $_   More variables to dump
     */
    public function dumpAll($var, $_ = null)
    {
        $this->dumpVars(func_get_args());
    }

    /**
     * Dumps backtrace
     *
     * @param int    $limit   Max traces to show
     * @param string $output  Output
     */
    public function dumpBacktrace($limit = null, $output = null)
    {
        $backtrace = $this->getBacktraceAsStream($limit);
        $this->write($backtrace, $output);
    }

    // Short hands
    /**
     * Dumps all array values and ends execution
     *
     * @param array  $var     Array of vars to dump to dump
     * @param int    $deep    Deep of dumpping
     * @param string $output  Output
     */
    public function edumpVars(array $vars, $deep = null, $output = null)
    {
        $this->dumpVars($vars, $deep, $output);
        $this->end();
    }

    /**
     * Dumps a variable and ends execution
     *
     * @param type   $var     Variable to dump
     * @param int    $deep    Deep of dumpping
     * @param string $output  Output
     */
    public function edump($var, $deep = null, $output = null)
    {
        $this->dumpVars(array($var), $deep, $output);
        $this->end();
    }

    /**
     * Dumps all arguments and ends execution
     *
     * @param mixed $var Variable to dump
     * @param mixed $_   More variables to dump
     */
    public function edumpAll($var, $_ = null)
    {
        $this->dumpVars(func_get_args());
        $this->end();
    }

    /**
     * Dumps backtrace and ends execution
     *
     * @param int    $limit   Max traces to show
     * @param string $output  Output
     */
    public function edumpBacktrace($limit = null, $output = null)
    {
        $this->dumpBacktrace($limit, $output);
        $this->end();
    }

    /**
     * Ends up execution
     */
    public function end()
    {
        call_user_func($this->endClousure);
    }


    /**
     * Gets backtrace as token stream
     *
     * @param int $limit Max traces to parse
     * @return TokenStream
     */
    protected function getBacktraceAsStream($limit = null)
    {
        return $this->backtraceParser->parseBacktrace($this->getBacktrace($limit), $this->varParser);
    }

    /**
     * Gets caller as token stream
     *
     * @return TokenStream
     */
    protected function getCallerAsStream()
    {
        return $this->backtraceParser->parseStackFrame($this->getCaller(), $this->varParser);
    }

    /**
     * Output given messages
     *
     * @param array       $messages Messages to output
     * @param string|null $output   Output to use
     */
    protected function write($messages, $output = null)
    {
        $output = $output ? : $this->defaultOutput;
        $caller = $this->getCallerAsStream();

        $this->getOutput($output)->write($messages, $caller);
    }

    /** @var Dumper */
    private static $instance = null;

    /**
     * Get singleton instance of dumper
     * @return Dumper
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            throw new \Exception('Dumper is not enabled');
        }

        return self::$instance;
    }

    /**
     * Get singleton instance of dumper
     * @return Dumper
     */
    public static function enable($context, $defineGlobalFunctions = true)
    {
        $varParser = new VarParser();
        $varParser->addTokenizer(new Tokenizer\GenericTokenizer());
        $varParser->addTokenizer(new Tokenizer\StringTokenizer());
        $varParser->addTokenizer(new Tokenizer\ArrayTokenizer());
        $varParser->addTokenizer(new Tokenizer\ObjectTokanizer());

        $backtraceParser = new BacktraceParser();


        $backtraceFactory = new Backtrace();
        $backtraceFactory->addExcule(__DIR__);
        $backtraceFactory->addExcule(__NAMESPACE__);

        self::$instance = new self($varParser, $backtraceParser, $backtraceFactory);

        self::$instance->addOutput('browser', new Output\BrowserOutput());
        self::$instance->addOutput('console', new Output\ConsoleOutput());
        self::$instance->setDefaultOutput($context);



        if ($defineGlobalFunctions) {
            static::defineGlobalFunctions();
        }

        return self::$instance;
    }

    /**
     * Define Dumper functions globally
     */
    public static function defineGlobalFunctions()
    {
        require __DIR__ . '/dumperGlobalFunctions.php';
    }

}