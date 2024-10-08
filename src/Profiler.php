<?php

declare(strict_types=1);

namespace PetrKnap\Profiler;

/* final */class Profiler implements ProfilerInterface
{
    /**
     * @var Profile<mixed>|null $parentProfile
     */
    protected Profile|null $parentProfile = null;

    public function profile(callable $callable): ProcessableProfileInterface & ProfileWithOutputInterface
    {
        $profile = new Profile();

        $profile->start();
        $output = $callable(new class ($profile) extends Profiler {
            /**
             * @param Profile<mixed> $parentProfile
             */
            public function __construct(Profile $parentProfile)
            {
                $this->parentProfile = $parentProfile;
            }
        });
        $profile->finish();
        $profile->setOutput($output);

        $this->parentProfile?->addChild($profile);

        return $profile;
    }
}
