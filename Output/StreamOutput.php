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
use Deg\Dumper\Formatter\PlainFormatter;

/**
 * StreamOutput
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class StreamOutput extends Output
{
    private $stream;

    /**
     * Constructor.
     *
     * @param FormatterInterface|null $formatter Output formatter instance (null to use default OutputFormatter)
     */
    public function __construct($stream, FormatterInterface $formatter = null)
    {
        $this->stream = $stream;


        parent::__construct($formatter);
    }

    protected function doWrite($message)
    {
        if (false === @fwrite($this->stream, $message .PHP_EOL)) {
            // should never happen
            throw new \RuntimeException('Unable to write output.');
        }

        fflush($this->stream);
    }


    /**
     * Returns true if the stream supports colorization.
     *
     * Colorization is disabled if not supported by the stream:
     *
     *  -  Windows without Ansicon and ConEmu
     *  -  non tty consoles
     *
     * @return bool true if the stream supports colorization, false otherwise
     */
    protected function hasColorSupport()
    {
        if (DIRECTORY_SEPARATOR == '\\') {
            return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
        }

        return function_exists('posix_isatty') && @posix_isatty($this->stream);
    }
}