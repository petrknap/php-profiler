<?php

namespace PetrKnap\Php\Profiler\Test;

use PetrKnap\Php\Profiler\Profile;

class ProfileTest extends \PHPUnit_Framework_TestCase
{
    private function getProfileAsArray(Profile $profile)
    {
        return json_decode(json_encode($profile), true);
    }

    public function testJsonSerializable()
    {
        $profile = $this->getProfileAsArray(new Profile());

        $this->assertArrayHasKey(Profile::ABSOLUTE_DURATION, $profile);
        $this->assertArrayHasKey(Profile::DURATION, $profile);
        $this->assertArrayHasKey(Profile::ABSOLUTE_MEMORY_USAGE_CHANGE, $profile);
        $this->assertArrayHasKey(Profile::MEMORY_USAGE_CHANGE, $profile);
    }

    public function testMetaConflict()
    {
        $absoluteDuration = 100;

        $profile = new Profile();
        $profile->meta[Profile::ABSOLUTE_DURATION] = $absoluteDuration;
        $profile->meta["meta_" . Profile::ABSOLUTE_DURATION] = $absoluteDuration;

        $profile = $this->getProfileAsArray($profile);

        $this->assertNotEquals($absoluteDuration, $profile[Profile::ABSOLUTE_DURATION]);
        $this->assertEquals($absoluteDuration, $profile["meta_" . Profile::ABSOLUTE_DURATION]);
    }
}
