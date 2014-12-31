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
use Deg\Dumper\Formatter\ConsoleFormatter;

/**
 * ConsoleOutput
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class ConsoleOutput extends StreamOutput
{
    /**
     * Constructor.
     *
     * @param FormatterInterface|null $formatter Output formatter instance (null to use default OutputFormatter)
     */
    public function __construct(FormatterInterface $formatter = null)
    {
        $outputStream = 'php://stdout';
        if (!$this->hasStdoutSupport()) {
            $outputStream = 'php://output';
        }

        $formatter = $formatter ? : new ConsoleFormatter();

        parent::__construct(fopen($outputStream, 'w'), $formatter);
    }


    /**
     * Returns true if current environment supports writing console output to
     * STDOUT.
     *
     * IBM iSeries (OS400) exhibits character-encoding issues when writing to
     * STDOUT and doesn't properly convert ASCII to EBCDIC, resulting in garbage
     * output.
     *
     * @return bool
     */
    protected function hasStdoutSupport()
    {
        return ('OS400' != php_uname('s'));
    }

}