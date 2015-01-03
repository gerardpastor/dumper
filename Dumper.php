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

use Deg\Dumper\Backtrace\BacktraceFactory;
use Deg\Dumper\Parser\VarParser;
use Deg\Dumper\Parser\BacktraceParser;
use Deg\Dumper\Output\OutputInterface;
use Deg\Dumper\Parser\TokenStream;
use Deg\Dumper\Parser\Tokenizer;
use Deg\Dumper\Parser\Tokenizer\TokenizerInterface;
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
     * @var BacktraceFactory
     */
    private $backtraceFactory;

    /**
     *
     * @var OutputInterface
     */
    private $output;

    /**
     *
     * @var clousure
     */
    private $endClousure;

    /**
     * Contruct.
     *
     * @param VarParser $varParser
     * @param BacktraceParser $backtraceParser
     * @param BacktraceFactory $backtraceFactory
     * @param OutputInterface $output
     * @param function|null $endClousure
     */
    public function __construct(VarParser $varParser, BacktraceParser $backtraceParser, BacktraceFactory $backtraceFactory, OutputInterface $output, $endClousure = null)
    {
        $this->varParser = $varParser;
        $this->backtraceParser = $backtraceParser;
        $this->backtraceFactory = $backtraceFactory;
        $this->output = $output;

        $this->endClousure = $endClousure ? : function () {
            die();
        };
    }

    /**
     *
     * @return VarParser
     */
    public function getVarParser() {
        return $this->varParser;
    }

    /**
     *
     * @return BacktraceParser
     */
    public function getBacktraceParse() {
        return $this->backtraceParser;
    }

    /**
     *
     * @return BacktraceFactory
     */
    public function getBacktraceFactory() {
        return $this->backtraceFactory;
    }

    /**
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
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
     * Gets default max dumping deep
     *
     * @return int
     */
    public function getDefaultMaxDeep()
    {
        return $this->varParser->getMaxDeep();
    }

    /**
     * Sets default max dumping deep
     *
     * @param int $maxDeep
     */
    public function setDefaultMaxDeep($maxDeep)
    {
        $this->varParser->setMaxDeep($maxDeep);
    }

    /**
     * Gets default max dumping limit
     *
     * @return int
     */
    public function getDefaultMaxLimit()
    {
        return $this->backtraceFactory->getMaxLimit();
    }

     /**
     * Sets default max dumping limit
     *
     * @param int $maxLimit
     */
    public function setDefaultMaxLimit($maxLimit)
    {
        $this->backtraceFactory->setMaxLimit($maxLimit);
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
     */
    public function dumpVars(array $vars, $deep = null)
    {
        $messages = array();
        foreach ($vars as $var) {
            $messages[] = $this->varParser->parse($var, $deep);
        }

        $this->write($messages);
    }

    /**
     * Dumps information about a variable
     *
     * @param type   $var     Variable to dump
     * @param int    $deep    Deep of dumpping
     */
    public function dump($var, $deep = null)
    {
        $this->dumpVars(array($var), $deep);
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
     * @param int    $limit   Max traces to dump
     */
    public function dumpBacktrace($limit = null)
    {
        $backtrace = $this->getBacktraceAsStream($limit);
        $this->write($backtrace);
    }

    // Short hands
    /**
     * Dumps all array values and ends execution
     *
     * @param array  $var     Array of vars to dump to dump
     * @param int    $deep    Deep of dumpping
     */
    public function edumpVars(array $vars, $deep = null)
    {
        $this->dumpVars($vars, $deep);
        $this->end();
    }

    /**
     * Dumps a variable and ends execution
     *
     * @param type   $var     Variable to dump
     * @param int    $deep    Deep of dumpping
     */
    public function edump($var, $deep = null)
    {
        $this->dumpVars(array($var), $deep);
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
     */
    public function edumpBacktrace($limit = null)
    {
        $this->dumpBacktrace($limit);
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
    protected function write($messages)
    {
        $caller = $this->getCallerAsStream();
        $this->output->write($messages, $caller);
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
    public static function enable($output, $defineGlobalFunctions = true)
    {
        $varParser = new VarParser();
        $varParser->addTokenizer(new Tokenizer\GenericTokenizer());
        $varParser->addTokenizer(new Tokenizer\StringTokenizer());
        $varParser->addTokenizer(new Tokenizer\ArrayTokenizer());
        $varParser->addTokenizer(new Tokenizer\ObjectTokanizer());

        $backtraceParser = new BacktraceParser();


        $backtraceFactory = new BacktraceFactory();
        $backtraceFactory->addExcule(__DIR__);
        $backtraceFactory->addExcule(__NAMESPACE__);

        if (!$output instanceof OutputInterface) {
            switch ($output) {
                case 'browser':
                    $output = new Output\BrowserOutput();
                    break;

                case 'console':
                    $output = new Output\ConsoleOutput();
                    break;

                default:
                    $output = new Output\NullOutput();
                    break;
            }
        }

        self::$instance = new self($varParser, $backtraceParser, $backtraceFactory, $output);

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