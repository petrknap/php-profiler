<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use LogicException;

/**
 * @todo rename to `ProfilingCouldNotBeFinished`
 * @todo remove implementation of {@see ProfilerException}
 */
final class ProfilingHasBeenAlreadyFinished extends LogicException implements ProfilerException, ProfilingException
{
}
