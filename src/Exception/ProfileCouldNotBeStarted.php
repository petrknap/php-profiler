<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use LogicException;

/**
 * @internal exception should never be thrown out
 */
final class ProfileCouldNotBeStarted extends LogicException implements ProfileException
{
}
