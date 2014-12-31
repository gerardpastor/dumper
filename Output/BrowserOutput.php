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

use Deg\Dumper\Formatter\HtmlFormatter;

/**
 * BrowserOutput
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class BrowserOutput extends Output
{
    /**
     * Constructor.
     *
     * @param FormatterInterface|null $formatter Output formatter instance (null to use default OutputFormatter)
     */
    public function __construct(\Deg\Dumper\Formatter\FormatterInterface $formatter = null)
    {
        $formatter = $formatter ? : new HtmlFormatter();

        parent::__construct($formatter);
    }

    protected function doWrite($message)
    {
        echo $message;
    }

}