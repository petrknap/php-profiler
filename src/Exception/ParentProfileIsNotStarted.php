<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use LogicException;

/**
 * @internal exception should never be thrown out
 *
 * @todo rename to `ProfilerCouldNotProfileOutsideParentProfile`
 */
final class ParentProfileIsNotStarted extends LogicException implements ProfilerException
{
}
