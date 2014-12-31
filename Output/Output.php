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

use Deg\Dumper\Parser\TokenStream;
use Deg\Dumper\Formatter\FormatterInterface;
use Deg\Dumper\Formatter\PlainFormatter;

/**
 * Output
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
abstract class Output implements OutputInterface
{
    /**
     *
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * Constructor.
     *
     * @param FormatterInterface|null $formatter Output formatter instance (null to use default OutputFormatter)
     */
    public function __construct(FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ? : new PlainFormatter();
    }

    /**
     * Sets output formatter.
     *
     * @param FormatterInterface $formatter
     *
     * @api
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Returns current output formatter instance.
     *
     * @return FormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Writes a message to the output.
     *
     * @param TokenStream|array $message The message to output
     * @param TokenStream       $caller  The caller of function
     * @param int               $type    The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function write($messages, TokenStream $caller = null, $type = self::OUTPUT_NORMAL)
    {
        $messages = is_array($messages) ? $messages : array($messages);

        switch ($type) {
            case OutputInterface::OUTPUT_NORMAL:

                $message = $this->formatter->format($messages, $caller);
                break;
            case OutputInterface::OUTPUT_RAW:
                $message = $caller . "\n" . implode("\n", $messages);
                break;
            case OutputInterface::OUTPUT_PLAIN:
                $message = strip_tags($this->formatter->format($messages, $caller));
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown output type given (%s)', $type));
        }

        $this->doWrite($message);
    }

    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     */
    abstract protected function doWrite($message);
}