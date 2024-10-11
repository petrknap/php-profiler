<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

interface ProfileInterface
{
    /**
     * @return array<ProfileInterface>
     */
    public function getChildren(): array;

    /**
     * @return float seconds
     */
    public function getDuration(): float;

    /**
     * @return int bytes
     */
    public function getMemoryUsageChange(): int;

    /**
     * @return array<numeric-string, int> bytes at {@see microtime}
     */
    public function getMemoryUsages(): array;
}
