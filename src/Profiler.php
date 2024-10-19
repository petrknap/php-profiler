<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/* final */class Profiler implements ProfilerInterface
{
    public function __construct(
        private readonly bool $takeSnapshotOnTick = Profile::DO_NOT_TAKE_SNAPSHOT_ON_TICK,
        /**
         * @deprecated backward compatibility with old named argument calls
         *
         * @todo remove it
         */
        private readonly bool $listenToTicks = Profile::DO_NOT_TAKE_SNAPSHOT_ON_TICK,
    ) {
    }

    public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
    {
        $profiling = Profiling::start($this->takeSnapshotOnTick, $this->listenToTicks);
        $output = $callable(Profiling::createNestedProfiler($profiling));
        /** @var Profile<mixed> $profile */
        $profile = $profiling->finish();
        $profile->setOutput($output);

        return $profile; // @phpstan-ignore return.type
    }

    public function takeSnapshot(): void
    {
        throw new Exception\ProfilerCouldNotTakeSnapshotOutsideParentProfile();
    }
}
