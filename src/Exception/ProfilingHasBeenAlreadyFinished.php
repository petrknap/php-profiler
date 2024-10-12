<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use LogicException;

final class ProfilingHasBeenAlreadyFinished extends LogicException implements ProfilerException
{
    use ThrowIf;
}
