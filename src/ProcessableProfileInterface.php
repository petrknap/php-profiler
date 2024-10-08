<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/**
 * @template TOutput of mixed
 */
interface ProcessableProfileInterface extends ProfileInterface
{
    /**
     * @param callable(ProfileInterface): mixed $processor
     *
     * @return TOutput
     */
    public function process(callable $processor): mixed;
}
