<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use LogicException;

final class ProfilerCouldNotSnapshotOutsideParentProfile extends LogicException implements ProfilerException
{
}
