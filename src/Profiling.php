<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

final class Profiling
{
    /**
     * @param Profile<mixed> $profile
     */
    private function __construct(
        private readonly Profile $profile,
    ) {
    }

    public static function start(): self
    {
        $profile = new Profile();
        $profile->start();

        return new self($profile);
    }

    /**
     * @throws Exception\ProfilingHasBeenAlreadyFinished
     */
    public function finish(): ProfileInterface
    {
        try {
            $this->profile->finish();

            return $this->profile;
        } catch (Exception\ProfileException $profileException) {
            throw new Exception\ProfilingHasBeenAlreadyFinished(previous: $profileException);
        }
    }

    /**
     * @internal should be used only by {@see Profiler::profile()}
     */
    public static function createNestedProfiler(Profiling $profiling): ProfilerInterface
    {
        return new class ($profiling->profile) extends Profiler {
            /**
             * @param Profile<mixed> $parentProfile
             */
            public function __construct(
                private readonly Profile $parentProfile,
            ) {
            }

            public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
            {
                if ($this->parentProfile->getState() !== ProfileState::Started) {
                    throw new Exception\ParentProfileIsNotStarted();
                }

                $profile = parent::profile($callable);
                $this->parentProfile->addChild($profile);

                return $profile;
            }
        };
    }
}
