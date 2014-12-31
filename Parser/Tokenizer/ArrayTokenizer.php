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
 * ArrayTokenizer
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class ArrayTokenizer implements TokenizerInterface
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

        $count = count($var);

        $builder
            ->addType('array')
            ->addBrace('(')
            ->addNumber($count)
            ->addBrace(')')
        ;


//        if ($deep < 0) {
//            return $builder->getStream();
//        }


        if (!$count or $deep < 0) {
            $builder
                ->addWhitespece()
                ->addBrace('{')
            ;

            if ($count) {
                $builder->addKeyword('…');
            }

            $builder->addBrace('}');

            return $builder->getStream();
        }


        $this->tokenizeItems($builder, $var, $deep, $parser);

        return $builder->getStream();
    }

    protected function tokenizeItems(TokenStreamBuilder $builder, $var, $deep, VarParser $parser)
    {
        $keyLen = array_reduce(array_keys($var), function($maxLen, $key) {
            $len = strlen($key . '') + (is_string($key) ? 2 : 0);

            return $len > $maxLen ? $len : $maxLen;
        });

        $builder
            ->addWhitespece()
            ->addBrace('{')
            ->addNewLine()
        ;

        foreach ($var as $key => $value) {

            $builder->addIndention();


//            $builder->addBrace('[');
            if (is_string($key)) {
                $builder->addString('"' . $key . '"');
            } else {
                $builder->addNumber($key);
            }
//            $builder->addBrace(']');

            $diff = $keyLen - strlen($key . '') - (is_string($key) ? 2 : 0);

            $builder
                ->addWhitespece($diff + 1)
                ->addPunctuation('=>')
                ->addWhitespece()
            ;


            $stream = $parser->parse($value, $deep - 1);
            $builder->addStream($stream);

            $builder->addNewLine();
        }

        $builder->addBrace('}');
    }

    /**
     *
     * @param mixed $var
     * @return boolean
     */
    public function accept($var)
    {
        return is_array($var);
    }

    /**
     *
     * @return int
     */
    public function getConfidence()
    {
        return 10;
    }

}