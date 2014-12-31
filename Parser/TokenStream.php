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

/**
 * TokenStream
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class TokenStream
{
    /**
     * @var Token[]
     */
    private $tokens = array();

    /**
     * @var Token[]
     */
    private $used = array();

    /**
     * @var int
     */
    private $cursor = 0;

    /**
     * @var Token|null
     */
    private $peeked = null;

    /**
     * @var bool
     */
    private $peeking = false;

    /**
     * Pushes a token.
     *
     * @param Token $token
     *
     * @return TokenStream
     */
    public function push(Token $token)
    {
        $this->tokens[] = $token;

        return $this;
    }

    public function append(TokenStream $stream)
    {
        $this->tokens = array_merge($this->tokens, $stream->getTokens());

        return $this;
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function indent($nb = 1)
    {
        $newStream = array();

        foreach ($this->tokens as $token) {
            $newStream[] = $token;

            if ($token->getType() === Token::TYPE_NEW_LINE) {
                for ($i = $nb; $i-- > 0;) {
                    $newStream[] = new Token(Token::TYPE_INDENTION);
                }
            }
        }

        $this->tokens = $newStream;

        return $this;
    }

    /**
     * Returns next token.
     *
     * @throws InternalErrorException If there is no more token
     *
     * @return Token
     */
    public function getNext()
    {
        if ($this->peeking) {
            $this->peeking = false;
            $this->used[] = $this->peeked;

            return $this->peeked;
        }

        if (!isset($this->tokens[$this->cursor])) {
            throw new Exception('Unexpected token stream end.');
        }

        return $this->tokens[$this->cursor ++];
    }

    public function hasNext()
    {
        return $this->peeking or isset($this->tokens[$this->cursor]);
    }

    /**
     * Returns peeked token.
     *
     * @return Token
     */
    public function getPeek()
    {
        if (!$this->peeking) {
            $this->peeked = $this->getNext();
            $this->peeking = true;
        }

        return $this->peeked;
    }

    /**
     * Returns used tokens.
     *
     * @return Token[]
     */
    public function getUsed()
    {
        return $this->used;
    }

    public function __toString()
    {
        $buffer = '';

        foreach ($this->tokens as $token) {
            $buffer .= $token;
        }

        return $buffer;
    }

}