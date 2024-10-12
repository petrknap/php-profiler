<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use Throwable;

/**
 * @internal trait can apply breaking changes within the same major version
 */
trait ThrowIf
{
    public static function throwIf(bool $condition): void
    {
        if ($condition) {
            throw new self();
        }
    }
}
