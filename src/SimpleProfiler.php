<?php

namespace PetrKnap\Php\Profiler;

use PetrKnap\Php\Profiler\Exception\EmptyStackException;

/**
 * Simple PHP class for profiling
 *
 * @author   Petr Knap <dev@petrknap.cz>
 * @since    2015-12-13
 * @license  https://github.com/petrknap/php-profiler/blob/master/LICENSE MIT
 */
class SimpleProfiler implements ProfilerInterface
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
    protected static $stack = [];

    /**
     * Enable profiler
     */
    public static function enable()
    {
        static::$enabled = true;
    }

    /**
     * Disable profiler
     */
    public static function disable()
    {
        static::$enabled = false;
    }

    /**
     * @return bool true if profiler is enabled, otherwise false
     */
    public static function isEnabled()
    {
        return static::$enabled;
    }

    /**
     * @inheritdoc
     */
    public static function start($labelOrFormat = null, $args = null, $_ = null)
    {
        if (static::$enabled) {
            if ($args === null) {
                $label = $labelOrFormat;
            } else {
                /** @noinspection SpellCheckingInspection */
                $label = call_user_func_array("sprintf", func_get_args());
            }

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

            array_push(static::$stack, $profile);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public static function finish($labelOrFormat = null, $args = null, $_ = null)
    {
        if (static::$enabled) {
            $now = microtime(true);
            $memoryUsage = memory_get_usage(true);

            if (empty(static::$stack)) {
                throw new EmptyStackException("The stack is empty. Call " . static::class . "::start() first.");
            }

            if ($args === null) {
                $label = $labelOrFormat;
            } else {
                /** @noinspection SpellCheckingInspection */
                $label = call_user_func_array("sprintf", func_get_args());
            }

            /** @var Profile $profile */
            $profile = array_pop(static::$stack);
            $profile->meta[self::FINISH_LABEL] = $label;
            $profile->meta[self::FINISH_TIME] = $now;
            $profile->meta[self::FINISH_MEMORY_USAGE] = $memoryUsage;
            $profile->absoluteDuration = $profile->meta[self::FINISH_TIME] - $profile->meta[self::START_TIME];
            $profile->duration = $profile->absoluteDuration - $profile->meta[self::TIME_OFFSET];
            $profile->absoluteMemoryUsageChange = $profile->meta[self::FINISH_MEMORY_USAGE] - $profile->meta[self::START_MEMORY_USAGE];
            $profile->memoryUsageChange = $profile->absoluteMemoryUsageChange - $profile->meta[self::MEMORY_USAGE_OFFSET];

            if (!empty(static::$stack)) {
                $timeOffset = &static::$stack[count(static::$stack) - 1]->meta[self::TIME_OFFSET];
                $timeOffset = $timeOffset + $profile->absoluteDuration;

                $memoryUsageOffset = &static::$stack[count(static::$stack) - 1]->meta[self::MEMORY_USAGE_OFFSET];
                $memoryUsageOffset = $memoryUsageOffset + $profile->absoluteMemoryUsageChange;
            }

            return $profile;
        }

        return false;
    }
}
