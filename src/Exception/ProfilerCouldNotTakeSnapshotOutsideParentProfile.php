<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use LogicException;

final class ProfilerCouldNotTakeSnapshotOutsideParentProfile extends LogicException implements ProfilerException
{
}
