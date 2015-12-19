<?php

namespace PetrKnap\Php\Profiler;

/**
 * Profiler interface
 *
 * @author   Petr Knap <dev@petrknap.cz>
 * @since    2015-12-19
 * @category Debug
 * @package  PetrKnap\Php\Profiler
 * @version  0.1
 * @license  https://github.com/petrknap/php-profiler/blob/master/LICENSE MIT
 */
interface ProfilerInterface
{
    #region Result keys
    const START_LABEL = "start_label"; // string
    const START_TIME = "start_time"; // float start time in seconds
    const START_MEMORY_USAGE = "start_memory_usage"; // int amount of used memory at start in bytes
    const FINISH_LABEL = "finish_label"; // string
    const FINISH_TIME = "finish_time"; // float finish time in seconds
    const FINISH_MEMORY_USAGE = "finish_memory_usage"; // int amount of used memory at finish in bytes
    const TIME_OFFSET = "time_offset"; // float time offset in seconds
    const MEMORY_USAGE_OFFSET = "memory_usage_offset"; // int amount of memory usage offset in bytes
    const ABSOLUTE_DURATION = "absolute_duration"; // float absolute duration in seconds
    const DURATION = "duration"; // float duration in seconds
    const ABSOLUTE_MEMORY_USAGE_CHANGE = "absolute_memory_usage_change"; // int absolute memory usage change in bytes
    const MEMORY_USAGE_CHANGE = "memory_usage_change"; // int memory usage change in bytes
    #endregion

    /**
     * Enable profiler
     */
    public static function enable();

    /**
     * Disable profiler
     */
    public static function disable();

    /**
     * Start profiling
     *
     * @param string $label
     * @return bool true on success or false on failure
     */
    public static function start($label = null);

    /**
     * Finish profiling and get result
     *
     * @param string $label
     * @return array|bool result as array on success or false on failure
     */
    public static function finish($label = null);
}
