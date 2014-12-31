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
 * StringTokenizer
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class StringTokenizer implements TokenizerInterface
{
    /**
     * Cut strings deeper than maxDeep
     * @var type
     */
    private $maxDeep;

    /**
     * Cut strings larger than maxLen
     * @var type
     */
    private $maxLen;

    /**
     * Construct.
     *
     * @param type $maxDeep
     * @param type $maxLen
     */
    public function __construct($maxDeep = 0, $maxLen = 100)
    {
        $this->maxDeep = $maxDeep;
        $this->maxLen = $maxLen;
    }

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

        if ($var == '' or $deep >= $this->maxDeep) {
            $builder->addString('"' . $var . '"');
            return $builder->getStream();
        }

        $lines = $lines = array_map('trim', explode("\n", $var));
        $firstLine = array_shift($lines);

        if ($this->maxLen and strlen($firstLine) > $this->maxLen) {
            $firstLine = substr($firstLine, 0, $this->maxLen) . '…';
        }

        $builder->addString('"' . $firstLine . '"', implode("\n", $lines));

        return $builder->getStream();
    }

    /**
     *
     * @param mixed $var
     * @return boolean
     */
    public function accept($var)
    {
        return is_string($var);
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