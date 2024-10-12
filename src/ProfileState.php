<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/**
 * @internal enum can apply breaking changes within the same major version
 */
enum ProfileState
{
    case Created;
    case Started;
    case Finished;
}
