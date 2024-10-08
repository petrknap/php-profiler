<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/**
 * @template TOutput of mixed
 */
interface ProfileWithOutputInterface extends ProfileInterface
{
    /**
     * @return TOutput
     */
    public function getOutput(): mixed;
}
