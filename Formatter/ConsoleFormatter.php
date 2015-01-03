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
 * ConsoleFormatter
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class ConsoleFormatter extends PlainFormatter
{
    private static $availableForegroundColors = array(
        'black' => array('set' => 30, 'unset' => 39),
        'red' => array('set' => 31, 'unset' => 39),
        'green' => array('set' => 32, 'unset' => 39),
        'yellow' => array('set' => 33, 'unset' => 39),
        'blue' => array('set' => 34, 'unset' => 39),
        'magenta' => array('set' => 35, 'unset' => 39),
        'cyan' => array('set' => 36, 'unset' => 39),
        'white' => array('set' => 37, 'unset' => 39),
    );

    const TEMPLATE_BLOCK = '<pre style="color: #222; background: #fcfcfc !important; border: 1px solid #ccc; padding: 10px; border-radius: 4px; text-align: left !important; overflow: hidden;">%s</pre>';
    const TEMPLATE_CALLER = '<code style="color: #999; display: block;  padding-bottom: 10px; font-size: 12px;">%s</code>';
    const TEMPLATE_MESSAGE = '<code style="display: block; overflow: auto; border-top: 1px solid #eee; padding-top: 10px; padding-bottom: 10px;">%s</code>';

    protected $colors = array(
        Token::TYPE_TYPE => 'green',
        Token::TYPE_KEYWORD => 'cyan',
        Token::TYPE_BRACE => 'green',
        Token::TYPE_PUNCTUATION => 'green',
        Token::TYPE_NUMBER => 'magenta',
        Token::TYPE_STRING => 'red',
//        Token::TYPE_PLAIN_TEXT => 'black',
    );

    public function formatToken(Token $token)
    {
        if (!isset($this->colors[$token->getType()])) {
            return $token->getValue();
        }

        $colorName = $this->colors[$token->getType()];

        if (!isset(static::$availableForegroundColors[$colorName])) {
            return $token->getValue();
        }

        $colorCodes = static::$availableForegroundColors[$colorName];

        return sprintf("\033[%sm%s\033[%sm", $colorCodes['set'], $token->getValue(), $colorCodes['unset']);
    }

}