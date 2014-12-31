<?php

/*
 * This file is part of the Deg package.
 *
 * (c) Gerard Pastor <gerardpastor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deg\Dumper\Parser\Tokenizer;

use Deg\Dumper\Parser\VarParser;
use Deg\Dumper\Parser\TokenStream;
use Deg\Dumper\Parser\TokenStreamBuilder;

/**
 * GenericTokenizer
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class GenericTokenizer implements TokenizerInterface
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
        $builder = new TokenStreamBuilder();

        switch (gettype($var)) {
            case 'object':
                if ($deep >= 0) {
                    $builder
                        ->addKeyword(get_class($var))
                        ->addWhitespece()
                        ->addBrace('{}')
                    ;
                } else {
                    $reflect = new \ReflectionClass($var);

                    $builder
                        ->addKeyword($reflect->getShortName())
                        ->addWhitespece()
                        ->addBrace('{}')
                    ;
                }

                break;

            case 'array':
                $builder
                    ->addType('array')
                    ->addBrace('(')
                    ->addNumber(count($var))
                    ->addBrace(')')
                    ->addWhitespece()
                    ->addBrace('{')
                ;

                if (count($var)) {
                    $builder->addKeyword('…');
                }

                $builder->addBrace('{}');

                break;

            case 'NULL':
                $builder->addKeyword('null');

                break;

            case 'boolean':
                $builder->addKeyword($var ? 'true' : 'false');

                break;

            case 'integer':
            case 'double':
                $builder->addNumber($var);
                break;

            case 'string':
                $builder->addString('"' . $var . '"');

                break;
        }

        return $builder->getStream();
    }

    /**
     *
     * @param mixed $var
     * @return boolean
     */
    public function accept($var)
    {
        return true;
    }

    /**
     *
     * @return int
     */
    public function getConfidence()
    {
        return 0;
    }


}
