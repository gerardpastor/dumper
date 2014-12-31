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
 * Token
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class Token
{
    const TYPE_PLAIN_TEXT = 'text';
    const TYPE_TYPE = 'type';
    const TYPE_KEYWORD = 'keyword';
    const TYPE_BRACE = 'brace';
    const TYPE_PUNCTUATION = 'punctuation';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_INDENTION = 'indention';
    const TYPE_NEW_LINE = 'new-line';
    const TYPE_WHITESPACE = 'whitespace';
    const CHAR_INDENTION = "\t";
    const CHAR_NEW_LINE = "\n";
    const CHAR_WHITESPACE = " ";

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $description;

    /**
     * @param int    $type
     * @param string $value
     * @param int    $position
     */
    public function __construct($type, $value = null, $description = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isPlainText()
    {
        return self::TYPE_PLAIN_TEXT === $this->type;
    }

    /**
     * @return bool
     */
    public function isType()
    {
        return self::TYPE_TYPE === $this->type;
    }

    /**
     * @return bool
     */
    public function isBrace()
    {
        return self::TYPE_BRACE === $this->type;
    }

    /**
     * @return bool
     */
    public function isKeyword()
    {
        return self::TYPE_KEYWORD === $this->type;
    }

    /**
     * @return bool
     */
    public function isNumber()
    {
        return self::TYPE_NUMBER === $this->type;
    }

    /**
     * @return bool
     */
    public function isString()
    {
        return self::TYPE_STRING === $this->type;
    }


    /**
     * @return bool
     */
    public function isWhitespace()
    {
        return self::TYPE_WHITESPACE === $this->type;
    }

    /**
     * @return bool
     */
    public function isIndention()
    {
        return self::TYPE_INDENTION === $this->type;
    }

    /**
     * @return bool
     */
    public function isNewLine()
    {
        return self::TYPE_NEW_LINE === $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        switch ($this->type) {
            case self::TYPE_INDENTION:
                return "    ";

            case self::TYPE_NEW_LINE:
                return "\n";

            case self::TYPE_WHITESPACE:
                return ' ';

            default:
                return $this->value;
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function __toString()
    {
        return (string) ($this->description ?: $this->getValue());
    }

}