<?php

/*
 * This file is part of the Deg package.
 *
 * (c) Gerard Pastor <gerardpastor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deg\Dumper\Output;

use Deg\Dumper\Formatter\FormatterInterface;
use Deg\Dumper\Parser\TokenStream;

/**
 * OutputInterface
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
interface OutputInterface
{
    const OUTPUT_NORMAL = 0;
    const OUTPUT_RAW = 1;
    const OUTPUT_PLAIN = 2;

    /**
     * Sets output formatter.
     *
     * @param FormatterInterface $formatter
     *
     * @api
     */
    public function setFormatter(FormatterInterface $formatter);
    /**
     * Returns current output formatter instance.
     *
     * @return FormatterInterface
     */
    public function getFormatter();
    /**
     * Writes a message to the output.
     *
     * @param TokenStream|array $message The message to output
     * @param TokenStream       $caller  The caller of function
     * @param int               $type    The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function write($messages, TokenStream $caller = null, $type = self::OUTPUT_NORMAL);
}