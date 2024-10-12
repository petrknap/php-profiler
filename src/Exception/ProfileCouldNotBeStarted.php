<?php

declare(strict_types=1);

namespace PetrKnap\Profiler\Exception;

use LogicException;

/**
 * @internal object can apply breaking changes within the same major version
 */
final class ProfileCouldNotBeStarted extends LogicException implements ProfileException
{
    use ThrowIf;
}
