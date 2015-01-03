<?php

/*
 * This file is part of the Deg package.
 *
 * (c) Gerard Pastor <gerardpastor@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deg\Dumper\Backtrace;

use Deg\Dumper\Parser\Token;
use Deg\Dumper\Parser\TokenStream;

/**
 * Backtrace
 *
 * @author Gerard Pastor <www.gerard-pastor.com>
 */
class BacktraceFactory
{
    /**
     *
     * @var array
     */
    private $excludes = array();

    /**
     *
     * @var int
     */
    private $maxLimit;

    public function __construct(array $excludes = array(), $maxLimit = 0)
    {
        $this->excludes = $excludes;
        $this->excludes[] = realpath(__DIR__ . '/..');
        $this->excludes[] = __NAMESPACE__;

        $this->maxLimit = $maxLimit;
    }

    /**
     * Add a namespace or directory to exclude from backtrace dumping
     *
     * @param type $exclude
     */
    public function addExcule($exclude)
    {
        $this->excludes[] = $exclude;
    }

    /**
     * Get all namespaces or directories exluded from backtrace dumping
     *
     * @return array
     */
    public function getExcludes()
    {
        return $this->excludes;
    }

    /**
     * Gets default max dumping limit
     *
     * @return int
     */
    public function getMaxLimit()
    {
        return $this->maxLimit;
    }

    /**
     * Sets default max dumping limit
     *
     * @param int $maxLimit
     */
    public function setMaxLimit($maxLimit)
    {
        $this->maxLimit = $maxLimit;
    }

    /**
     * Gets backtrace
     * @param  int   $limit Max traces to show
     * @return array
     */
    public function get($limit = null)
    {
        $limit = (null !== $limit ? $limit : $this->maxLimit);
        $backtrace = debug_backtrace(null, $limit ? $limit + 1 : 0);

        return $this->format($backtrace);
    }

    /**
     *
     * @param array $backtrace
     * @return type
     */
    public function format($backtrace)
    {
        $formattedBacktrace = array();

        $oldTrace = array_shift($backtrace);
        foreach ($backtrace as $stackFrame) {

            $excluded = false;
            $file = isset($oldTrace['file']) ? $oldTrace['file'] : null;
            $class = isset($stackFrame['class']) ? $stackFrame['class'] : null;

            foreach ($this->excludes as $exclude) {
                if ($file and strpos($file, $exclude) === 0 or $class and strpos($class, $exclude) === 0) {
                    $excluded = true;
                }
            }

            if (!$excluded) {
                $formattedBacktrace[] = array(
                    'file' => $file,
                    'line' => isset($oldTrace['line']) ? $oldTrace['line'] : null,
                    'class' => $class,
                    'type' => isset($stackFrame['type']) ? $stackFrame['type'] : null,
                    'function' => $stackFrame['function'],
                    'args' => $stackFrame['args'],
                );
            }

            $oldTrace = $stackFrame;
        }

        return $formattedBacktrace;
    }

}