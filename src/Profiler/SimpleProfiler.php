<?php

namespace PetrKnap\Php\Profiler;

/**
 * Simple PHP class for profiling
 *
 * @author   Petr Knap <dev@petrknap.cz>
 * @since    2015-12-13
 * @license  https://github.com/petrknap/php-profiler/blob/master/LICENSE MIT
 */
class SimpleProfiler
{
    #region Meta keys
    const START_LABEL = "start_label"; // string
    const START_TIME = "start_time"; // float start time in seconds
    const START_MEMORY_USAGE = "start_memory_usage"; // int amount of used memory at start in bytes
    const FINISH_LABEL = "finish_label"; // string
    const FINISH_TIME = "finish_time"; // float finish time in seconds
    const FINISH_MEMORY_USAGE = "finish_memory_usage"; // int amount of used memory at finish in bytes
    const TIME_OFFSET = "time_offset"; // float time offset in seconds
    const MEMORY_USAGE_OFFSET = "memory_usage_offset"; // int amount of memory usage offset in bytes
    #endregion

    /**
     * @var bool
     */
    protected static $enabled = false;

    /**
     * @var Profile[]
     */
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
        if (self::$enabled) {
            $now = microtime(true);
            $memoryUsage = memory_get_usage(true);

            $profile = new Profile();
            $profile->meta = [
                self::START_LABEL => $label,
                self::TIME_OFFSET => 0,
                self::MEMORY_USAGE_OFFSET => 0,
                self::START_TIME => $now,
                self::START_MEMORY_USAGE => $memoryUsage
            ];

            array_push(self::$stack, $profile);

            return true;
        }

        return false;
    }

    /**
     * Finish profiling and get result
     *
     * @param string $label
     * @return Profile|bool profile on success or false on failure
     */
    public static function finish($label = null)
    {
        if (self::$enabled) {
            $now = microtime(true);
            $memoryUsage = memory_get_usage(true);

            if (empty(self::$stack)) {
                throw new \OutOfRangeException("Call " . __CLASS__ . "::start() first.");
            }

            /** @var Profile $profile */
            $profile = array_pop(self::$stack);
            $profile->meta[self::FINISH_LABEL] = $label;
            $profile->meta[self::FINISH_TIME] = $now;
            $profile->meta[self::FINISH_MEMORY_USAGE] = $memoryUsage;
            $profile->absoluteDuration = $profile->meta[self::FINISH_TIME] - $profile->meta[self::START_TIME];
            $profile->duration = $profile->absoluteDuration - $profile->meta[self::TIME_OFFSET];
            $profile->absoluteMemoryUsageChange = $profile->meta[self::FINISH_MEMORY_USAGE] - $profile->meta[self::START_MEMORY_USAGE];
            $profile->memoryUsageChange = $profile->absoluteMemoryUsageChange - $profile->meta[self::MEMORY_USAGE_OFFSET];

            if (!empty(self::$stack)) {
                $timeOffset = &self::$stack[count(self::$stack) - 1]->meta[self::TIME_OFFSET];
                $timeOffset = $timeOffset + $profile->absoluteDuration;

                $memoryUsageOffset = &self::$stack[count(self::$stack) - 1]->meta[self::MEMORY_USAGE_OFFSET];
                $memoryUsageOffset = $memoryUsageOffset + $profile->absoluteMemoryUsageChange;
            }

            return $profile;
        }

        return false;
    }
}
