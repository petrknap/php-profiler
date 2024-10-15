<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use LogicException;

/**
 * @todo remove implementation of {@see ProfilerException}
 */
final class ProfilingHasBeenAlreadyFinished extends LogicException implements ProfilerException, ProfilingException
{
}
