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
 * HtmlFormatter
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class HtmlFormatter extends PlainFormatter
{
    const TEMPLATE_BLOCK = '<pre style="color: #222; background: #fcfcfc !important; border: 1px solid #ccc; padding: 10px; border-radius: 4px; text-align: left !important; overflow: hidden;">%s</pre>';
    const TEMPLATE_CALLER = '<code style="color: #999; display: block;  padding-bottom: 10px; font-size: 12px;">%s</code>';
    const TEMPLATE_MESSAGE = '<code style="display: block; overflow: auto; border-top: 1px solid #eee; padding-top: 10px; padding-bottom: 10px;">%s</code>';

    private $colors = array(
        Token::TYPE_TYPE => '#007700',
        Token::TYPE_KEYWORD => '#0000BB',
        Token::TYPE_BRACE => '#007700',
        Token::TYPE_PUNCTUATION => '#007700',
        Token::TYPE_NUMBER => '#0000BB',
        Token::TYPE_STRING => '#DD0000',
        Token::TYPE_PLAIN_TEXT => '#000000',
    );

    public function format($messages, TokenStream $caller = null)
    {
        $messages = is_array($messages) ? $messages : array($messages);
        $output = '';



        foreach ($messages as $message) {
            $output .= sprintf(static::TEMPLATE_MESSAGE, $this->formatStream($message));
        }

        if ($caller) {
            $output = sprintf(static::TEMPLATE_CALLER, $this->formatStream($caller)) . $output;
        }

        return sprintf(static::TEMPLATE_BLOCK, $output);
    }

    public function formatToken(Token $token)
    {
        $attrs = array();
        $color = isset($this->colors[$token->getType()]) ? $this->colors[$token->getType()] : $this->colors[Token::TYPE_PLAIN_TEXT];

        $attrs['style'] = 'color: ' . $color;

        $description = $token->getDescription();
        if ($description) {
            $attrs['title'] = htmlentities($description);
        }

        return $this->formatTag($token->getValue(), $attrs);
    }

    protected function formatTag($value, $attrs) {
        $attrsStr = '';
        foreach ($attrs as $key => $val) {
            $attrsStr .= sprintf(' %s="%s"', $key, $val);
        }

        return sprintf('<span%s">%s</span>', $attrsStr, htmlentities($value));
    }

    protected function getWhitespace()
    {
        return '&nbsp;';
    }

    protected function getNewLine()
    {
        return "\n";
    }

    protected function getIndentation()
    {
        return '&nbsp;&nbsp;&nbsp;&nbsp;';
    }

}