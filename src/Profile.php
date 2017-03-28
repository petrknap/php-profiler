<?php

namespace PetrKnap\Php\Profiler;

use JsonSerializable;
use PetrKnap\Php\Profiler\Exception\MissingProfilerException;
use PetrKnap\Php\Profiler\Exception\ProfileException;
use PetrKnap\Php\Profiler\Exception\UnsupportedProfilerException;

/**
 * Profile
 *
 * @author   Petr Knap <dev@petrknap.cz>
 * @since    2015-12-19
 * @license  https://github.com/petrknap/php-profiler/blob/master/LICENSE MIT
 */
class Profile implements JsonSerializable, ProfilerInterface
{
    #region JSON keys
    const ABSOLUTE_DURATION = "absolute_duration";
    const DURATION = "duration";
    const ABSOLUTE_MEMORY_USAGE_CHANGE = "absolute_memory_usage_change";
    const MEMORY_USAGE_CHANGE = "memory_usage_change";
    #endregion

    /**
     * @var array
     */
    public $meta = [];

    /**
     * Absolute duration in seconds
     *
     * @var float
     */
    public $absoluteDuration;

    /**
     * Duration in seconds
     *
     * @var float
     */
    public $duration;

    /**
     * Absolute memory usage change in bytes
     *
     * @var int
     */
    public $absoluteMemoryUsageChange;

    /**
     * Memory usage change in bytes
     *
     * @var int
     */
    public $memoryUsageChange;

    /**
     * @var string
     */
    protected static $profilerClassName;

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return array_merge(
            $this->meta,
            [
                self::ABSOLUTE_DURATION => $this->absoluteDuration,
                self::DURATION => $this->duration,
                self::ABSOLUTE_MEMORY_USAGE_CHANGE => $this->absoluteMemoryUsageChange,
                self::MEMORY_USAGE_CHANGE => $this->memoryUsageChange
            ]
        );
    }

    /**
     * @param string $profilerClassName
     * @throws ProfileException
     */
    public static function setProfiler($profilerClassName)
    {
        if (!class_exists($profilerClassName)) {
            throw new MissingProfilerException("Class {$profilerClassName} not found");
        }
        if (!is_subclass_of($profilerClassName, ProfilerInterface::class) || is_subclass_of($profilerClassName, self::class)) {
            throw new UnsupportedProfilerException("Class {$profilerClassName} is not supported");
        }
        static::$profilerClassName = $profilerClassName;
    }

    /**
     * @inheritdoc
     */
    public static function start($labelOrFormat = null, $args = null, $_ = null)
    {
        if (!static::$profilerClassName) {
            throw new MissingProfilerException("Missing profiler");
        }
        return call_user_func_array([static::$profilerClassName, "start"], func_get_args());
    }

    /**
     * @inheritdoc
     */
    public static function finish($labelOrFormat = null, $args = null, $_ = null)
    {
        if (!static::$profilerClassName) {
            throw new MissingProfilerException("Missing profiler");
        }
        return call_user_func_array([static::$profilerClassName, "finish"], func_get_args());
    }
}
