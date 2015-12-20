<?php

namespace PetrKnap\Php\Profiler;

/**
 * Advanced PHP class for profiling
 *
 * @author   Petr Knap <dev@petrknap.cz>
 * @since    2015-12-19
 * @category Debug
 * @package  PetrKnap\Php\Profiler
 * @version  0.2
 * @license  https://github.com/petrknap/php-profiler/blob/master/LICENSE MIT
 */
class AdvancedProfiler extends SimpleProfiler implements ProfilerInterface
{
    /**
     * @var callable
     */
    private static $postProcessor = null;

    /**
     * Set post processor
     *
     * Post processor is callable with one input argument (return from finish method) and is called at the end of finish method.
     *
     * @param callable $postProcessor
     */
    public static function setPostProcessor(callable $postProcessor)
    {
        self::$postProcessor = $postProcessor;
    }

    /**
     * Get current "{file}#{line}"
     *
     * @return string|bool current "{file}#{line}" on success or false on failure
     */
    public static function getCurrentFileHashLine()
    {
        $args = func_get_args();

        $deep = &$args[0];

        $backtrace = debug_backtrace();
        $backtrace = &$backtrace[$deep ? $deep : 0];

        if ($backtrace) {
            return sprintf(
                "%s#%s",
                $backtrace["file"],
                $backtrace["line"]
            );
        }

        return false;
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
            if ($label === null) {
                $label = self::getCurrentFileHashLine(1);
            }

            return parent::start($label);
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
        if (self::$enabled) {
            if ($label === null) {
                $label = self::getCurrentFileHashLine(1);
            }

            $result = parent::finish($label);

            if (self::$postProcessor === null) {
                return $result;
            }

            return call_user_func(self::$postProcessor, $result);
        }

        return false;
    }
}
