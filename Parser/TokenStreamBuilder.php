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
 * TokenStreamBuilder
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class TokenStreamBuilder
{
    /**
     *
     * @var TokenStream
     */
    private $stream;

    /**
     * Contruct.
     * 
     */
    public function __construct()
    {
        $this->stream = new TokenStream();
    }

    /**
     *
     * @return TokenStream
     */
    public function getStream()
    {
        return clone $this->stream;
    }

    /**
     *
     * @param string $text
     * @param string $description
     * @return TokenStreamBuilder
     */
    public function addPlainText($text, $description = null)
    {
        $token = new Token(Token::TYPE_PLAIN_TEXT, $text, $description);
        $this->addToken($token);

        return $this;
    }

    /**
     *
     * @param string $type
     * @param string $description
     * @return TokenStreamBuilder
     */
    public function addType($type, $description = null)
    {
        $token = new Token(Token::TYPE_TYPE, $type, $description);
        $this->addToken($token);

        return $this;
    }

    /**
     *
     * @param string $keyword
     * @param string $description
     * @return TokenStreamBuilder
     */
    public function addKeyword($keyword, $description = null)
    {
        $token = new Token(Token::TYPE_KEYWORD, $keyword, $description);
        $this->addToken($token);

        return $this;
    }

    /**
     *
     * @param string $string
     * @param string $description
     * @return TokenStreamBuilder
     */
    public function addString($string, $description = null)
    {
        $token = new Token(Token::TYPE_STRING, $string, $description);
        $this->addToken($token);

        return $this;
    }

    /**
     *
     * @param int $number
     * @param string $description
     * @return TokenStreamBuilder
     */
    public function addNumber($number, $description = null)
    {
        $token = new Token(Token::TYPE_NUMBER, $number, $description);
        $this->addToken($token);

        return $this;
    }

    /**
     *
     * @param string $punctuation
     * @return TokenStreamBuilder
     */
    public function addPunctuation($punctuation)
    {
        $token = new Token(Token::TYPE_PUNCTUATION, $punctuation);
        $this->addToken($token);

        return $this;
    }

    /**
     *
     * @param string $brace
     * @return TokenStreamBuilder
     */
    public function addBrace($brace)
    {
        $token = new Token(Token::TYPE_BRACE, $brace);
        $this->addToken($token);

        return $this;
    }

    /**
     *
     * @param int $nb
     * @return TokenStreamBuilder
     */
    public function addWhitespece($nb = 1)
    {
        for ($i = $nb; $i-- > 0;) {
            $token = new Token(Token::TYPE_WHITESPACE);
            $this->addToken($token);
        }

        return $this;
    }

    /**
     *
     * @param int $nb
     * @return TokenStreamBuilder
     */
    public function addNewLine($nb = 1)
    {
        for ($i = $nb; $i-- > 0;) {
            $token = new Token(Token::TYPE_NEW_LINE);
            $this->addToken($token);
        }

        return $this;
    }

    /**
     *
     * @param int $nb
     * @return TokenStreamBuilder
     */
    public function addIndention($nb = 1)
    {
        for ($i = $nb; $i-- > 0;) {
            $token = new Token(Token::TYPE_INDENTION);
            $this->addToken($token);
        }

        return $this;
    }

    /**
     *
     * @param Token $token
     * @return TokenStreamBuilder
     */
    public function addToken(Token $token)
    {
        $this->stream->push($token);

        return $this;
    }

    /**
     *
     * @param TokenStream $stream
     * @param int $indention
     * @return TokenStreamBuilder
     */
    public function addStream(TokenStream $stream, $indention = 1)
    {
        if ($indention > 0) {
            $stream->indent($indention);
        }

        $this->stream->append($stream);

        return $this;
    }

}