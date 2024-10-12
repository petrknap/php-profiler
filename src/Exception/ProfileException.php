<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use Throwable;

/**
 * @internal interface can apply breaking changes within the same major version
 */
interface ProfileException extends Throwable
{
}
