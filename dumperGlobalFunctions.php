<?php

if (!function_exists('rawDump')) {

    /**
     * Dumps raw information about a variable
     *
     * @param type $var The variable you want to dump.
     * @param type $_
     */
    function rawDump($var, $_ = null)
    {
        echo '<pre>';
        call_user_func_array('var_dump', func_get_args());
        echo '</pre>';
    }

}

if (!function_exists('dumpVars')) {

    /**
     * Dumps all array values
     *
     * @param array  $var     Array of vars to dump
     * @param int    $deep    Deep of dumpping
     * @param string $output  Output
     */
    function dumpVars(array $vars, $deep = null, $output = null)
    {
        \Deg\Dumper\Dumper::getInstance()->dumpVars($vars, $deep, $output);
    }

}

if (!function_exists('dump')) {

    /**
     * Dumps information about a variable
     *
     * @param type   $var     Variable to dump
     * @param int    $deep    Deep of dumpping
     * @param string $output  Output
     */
    function dump($var, $deep = null, $output = null)
    {
        \Deg\Dumper\Dumper::getInstance()->dump($var, $deep, $output);
    }

}

if (!function_exists('dumpAll')) {

    /**
     * Dumps all arguments
     *
     * @param mixed $var Variable to dump
     * @param mixed $_   More variables to dump
     */
    function dumpAll($var, $_ = null)
    {
        \Deg\Dumper\Dumper::getInstance()->dumpVars(func_get_args());
    }

}

if (!function_exists('dumpBacktrace')) {

    /**
     * Dumps backtrace
     *
     * @param int    $limit   Max traces to show
     * @param string $output  Output
     */
    function dumpBacktrace($limit = null, $output = null)
    {
        \Deg\Dumper\Dumper::getInstance()->dumpBacktrace($limit, $output);
    }

}

// Short hands

if (!function_exists('edumpVars')) {

    /**
     * Dumps all array values and ends execution
     *
     * @param array  $var     Array of vars to dump to dump
     * @param int    $deep    Deep of dumpping
     * @param string $output  Output
     */
    function edumpVars(array $vars, $deep = null, $output = null)
    {
        \Deg\Dumper\Dumper::getInstance()->edumpVars($vars, $deep, $output);
    }

}

if (!function_exists('edump')) {

    /**
     * Dumps a variable and ends execution
     *
     * @param type   $var     Variable to dump
     * @param int    $deep    Deep of dumpping
     * @param string $output  Output
     */
    function edump($var, $deep = null, $output = null)
    {
        \Deg\Dumper\Dumper::getInstance()->edump($var, $deep, $output);
    }

}

if (!function_exists('edumpAll')) {

    /**
     * Dumps all arguments and ends execution
     *
     * @param mixed $var Variable to dump
     * @param mixed $_   More variables to dump
     */
    function edumpAll($var, $_ = null)
    {
        \Deg\Dumper\Dumper::getInstance()->edumpVars(func_get_args());
    }

}

if (!function_exists('edumpBacktrace')) {

    /**
     * Dumps backtrace and ends execution
     *
     * @param int    $limit   Max traces to show
     * @param string $output  Output
     */
    function edumpBacktrace($limit = null, $output = null)
    {
        \Deg\Dumper\Dumper::getInstance()->edumpBacktrace($limit, $output);
    }

}
