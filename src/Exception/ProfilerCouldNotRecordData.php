<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use RuntimeException;

final class ProfilerCouldNotRecordData extends RuntimeException implements ProfilerException
{
}
