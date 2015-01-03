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

use Deg\Dumper\Parser\Tokenizer\TokenizerInterface;

/**
 * VarParser
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class VarParser
{
    /**
     *
     * @var int
     */
    private $maxDeep = 3;

    /**
     *
     * @var array|TokenizerInterface[]
     */
    private $tokenizers = array();

    /**
     * Adds a var tokenizer
     *
     * @param  TokenizerInterface $tokenizer
     * @return Pool
     */
    public function addTokenizer(TokenizerInterface $tokenizer)
    {
        $this->tokenizers[] = $tokenizer;

        return $this;
    }

    /**
     * Get all tokenizers
     *
     * @return array|TokenizerInterface[]
     */
    public function getTokenizers()
    {
        return $this->tokenizers;
    }

    /**
     * Gets default max dumping deep
     *
     * @return int
     */
    public function getMaxDeep()
    {
        return $this->maxDeep;
    }

    /**
     * Sets default max dumping deep
     *
     * @param int $maxDeep
     */
    public function setMaxDeep($maxDeep)
    {
        $this->maxDeep = $maxDeep;
    }

    /**
     *
     * @param mixed $var
     * @param integer $deep
     * @return TokenStream
     */
    public function parse($var, $deep = null)
    {
        $deep = null !== $deep ? $deep : $this->maxDeep;
        $tokenizer = $this->guessTokenizer($var);

        return $tokenizer->tokenize($var, $deep, $this);
    }

    /**
     * Guess the best tokenizer for a given var
     *
     * @param mixed $var
     * @return TokenizerInterface
     * @throws \Exception
     */
    public function guessTokenizer($var)
    {
        $tokenizer = null;

        foreach ($this->tokenizers as $suggestedTokenizer) {
            if ($suggestedTokenizer->accept($var) and ($tokenizer === null or $tokenizer->getConfidence() < $suggestedTokenizer->getConfidence())) {
                $tokenizer = $suggestedTokenizer;
            }
        }

        if (!$tokenizer) {
            throw new \Exception('No valid tokenizer found');
        }

        return $tokenizer;
    }

}