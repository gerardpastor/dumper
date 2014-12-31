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

/**
 * TokenizerInterface
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
interface TokenizerInterface {

    /**
     *
     * @param mixed $var
     * @param integer $deep
     * @param VarParser $parser
     * @return TokenStream
     */
    public function tokenize($var, $deep, VarParser $parser);

    /**
     *
     * @param mixed $var
     * @return boolean
     */
    public function accept($var);

    /**
     *
     * @return int
     */
    public function getConfidence();
}