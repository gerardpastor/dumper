<?php

/*
 * This file is part of the Deg package.
 *
 * (c) Gerard Pastor <gerardpastor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deg\Dumper\Parser;

use Deg\Dumper\Parser\VarParser;
use Deg\Dumper\Parser\TokenStreamBuilder;

/**
 * BacktraceParser
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class BacktraceParser
{
    /**
     *
     * @param array $backtrace
     * @return TokenStream
     */
    public function parseBacktrace(array $backtrace, VarParser $varParser)
    {
        $builder = new TokenStreamBuilder();
        $i = 0;

        foreach ($backtrace as $stackFrame) {
            $builder
                ->addPlainText('#' . ++$i)
                ->addWhitespece()
                ->addStream($this->parseStackFrame($stackFrame, $varParser))
                ->addNewLine()
            ;
        }

        return $builder->getStream();
    }

    /**
     *
     * @param array $stackFrame
     * @return TokenStream
     */
    public function parseStackFrame(array $stackFrame, VarParser $varParser)
    {
        $builder = new TokenStreamBuilder();

        if (isset($stackFrame['class'])) {
            $reflect = new \ReflectionClass($stackFrame['class']);
            $builder
                ->addKeyword($reflect->getShortName(), $stackFrame['class'])
                ->addPunctuation($stackFrame['type'])
            ;
        }

        if (isset($stackFrame['function'])) {

            $args = array();
            foreach ($stackFrame['args'] as $arg) {
                $args[] = $varParser->parse($arg, -1);
            }

            $builder
                ->addKeyword($stackFrame['function'])
                ->addBrace('(')
            ;

            if (count($args)) {
                $builder->addStream(array_shift($args));

                while (count($args)) {
                    $builder
                        ->addPunctuation(',')
                        ->addWhitespece()
                        ->addStream(array_shift($args))
                    ;
                }
            }

            $builder->addBrace(')');
        }

        if (isset($stackFrame['file'])) {

            if (isset($stackFrame['class']) or isset($stackFrame['function'])) {
                $builder
                    ->addWhitespece()
                    ->addPlainText('in')
                    ->addWhitespece()
                ;
            }

            $builder->addKeyword(basename($stackFrame['file']), $stackFrame['file']);

            if (isset($stackFrame['line'])) {
                $builder
                    ->addWhitespece()
                    ->addPlainText('line')
                    ->addWhitespece()
                    ->addKeyword($stackFrame['line'])
                ;
            }
        }

        return $builder->getStream();
    }

}