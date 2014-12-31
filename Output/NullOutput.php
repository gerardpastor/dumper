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
 * NullOutput
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class NullOutput extends Output
{
    protected function doWrite($message)
    {

    }

}