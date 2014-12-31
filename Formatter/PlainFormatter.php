<?php

/*
 * This file is part of the Deg package.
 *
 * (c) Gerard Pastor <gerardpastor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deg\Dumper\Formatter;

use Deg\Dumper\Parser\Token;
use Deg\Dumper\Parser\TokenStream;

/**
 * PlainFormatter
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class PlainFormatter implements FormatterInterface
{
    public function format($messages, TokenStream $caller = null)
    {
        $messages = (array) $messages;
        $output = '';

        foreach ($messages as $message) {
            $output .= $this->formatStream($message) . str_repeat($this->getNewLine(), 2);
        }

        if ($caller) {
            $output = $this->formatStream($caller) . str_repeat($this->getNewLine(), 2) . $output;
        }

        return $output;
    }

    public function formatStream(TokenStream $stream)
    {
        $buffer = '';

        while ($stream->hasNext()) {
            $token = $stream->getNext();

            switch ($token->getType()) {
                case Token::TYPE_INDENTION:
                    $buffer .= $this->getIndentation();

                    break;

                case Token::TYPE_WHITESPACE:
                    $buffer .= $this->getWhitespace();

                    break;

                case Token::TYPE_NEW_LINE:
                    $buffer .= $this->getNewLine();

                    break;

                default:
                    $buffer .= $this->formatToken($token);

                    break;
            }
        }

        return $buffer;
    }

    public function formatToken(Token $token)
    {
        return $token->getDescription() ? : $token->getValue();
    }

    protected function getWhitespace()
    {
        return ' ';
    }

    protected function getNewLine()
    {
        return "\n";
    }

    protected function getIndentation()
    {
        return "    ";
    }

}