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
 * FormatterInterface
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
interface FormatterInterface
{
    public function format($messages, TokenStream $caller = null);
    public function formatToken(Token $token);
}