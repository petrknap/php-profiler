<?php

namespace PetrKnap\Php\Profiler;

/**
 * Simple PHP class for profiling
 *
 * @author   Petr Knap <dev@petrknap.cz>
 * @since    2015-12-13
 * @category Debug
 * @package  PetrKnap\Php\Profiler
 * @version  0.6
 * @license  https://github.com/petrknap/php-profiler/blob/master/LICENSE MIT
 */
class SimpleProfiler implements ProfilerInterface
{
    protected static $enabled = false;

    private static $stack = [];

    /**
     * Enable profiler
     */
    public static function enable()
    {
        self::$enabled = true;
    }

    /**
     * Disable profiler
     */
    public static function disable()
    {
        self::$enabled = false;
    }

    /**
     * Start profiling
     *
     * @param string $label
     * @return bool true on success or false on failure
     */
    public static function start($label = null)
    {
        if(self::$enabled) {
            $now = microtime(true);
            $memoryUsage = memory_get_usage(true);

            array_push(self::$stack, [
                self::START_LABEL => $label,
                self::TIME_OFFSET => 0,
                self::START_TIME => $now,
                self::MEMORY_USAGE_OFFSET => 0,
                self::START_MEMORY_USAGE => $memoryUsage
            ]);

            return true;
        }

        return false;
    }

    /**
     * Finish profiling and get result
     *
     * @param string $label
     * @return array|bool result as array on success or false on failure
     */
    public static function finish($label = null)
    {
        if(self::$enabled) {
            $now = microtime(true);
            $memoryUsage = memory_get_usage(true);

            if (empty(self::$stack)) {
                throw new \OutOfRangeException("Call " . __CLASS__ . "::start() first.");
            }

            $result = array_pop(self::$stack);

            $result[self::FINISH_LABEL] = $label;
            $result[self::FINISH_TIME] = $now;
            $result[self::FINISH_MEMORY_USAGE] = $memoryUsage;
            $result[self::ABSOLUTE_DURATION] = $result[self::FINISH_TIME] - $result[self::START_TIME];
            $result[self::DURATION] = $result[self::ABSOLUTE_DURATION] - $result[self::TIME_OFFSET];
            $result[self::ABSOLUTE_MEMORY_USAGE_CHANGE] = $result[self::FINISH_MEMORY_USAGE] - $result[self::START_MEMORY_USAGE];
            $result[self::MEMORY_USAGE_CHANGE] = $result[self::ABSOLUTE_MEMORY_USAGE_CHANGE] - $result[self::MEMORY_USAGE_OFFSET];

            if (!empty(self::$stack)) {
                $timeOffset = &self::$stack[count(self::$stack) - 1][self::TIME_OFFSET];
                $timeOffset = $timeOffset + $result[self::ABSOLUTE_DURATION];

                $memoryUsageOffset = &self::$stack[count(self::$stack) - 1][self::MEMORY_USAGE_OFFSET];
                $memoryUsageOffset = $memoryUsageOffset + $result[self::ABSOLUTE_MEMORY_USAGE_CHANGE];
            }

            return $result;
        }

        return false;
    }
}
