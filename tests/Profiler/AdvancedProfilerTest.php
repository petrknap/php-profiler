<?php

use PetrKnap\Php\Profiler\AdvancedProfiler;

class AdvancedProfilerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        AdvancedProfiler::enable();
    }

    public function testGetCurrentFileHashLine()
    {
        $this->assertEquals(
            sprintf(
                "%s#%s",
                __FILE__,
                22
            ),
            AdvancedProfiler::getCurrentFileHashLine()
        );

        $this->assertFalse(AdvancedProfiler::getCurrentFileHashLine(-999));
        $this->assertFalse(AdvancedProfiler::getCurrentFileHashLine(+999));
    }

    public function testAutomaticGenerationOfLabels()
    {
        AdvancedProfiler::start();
        $result = AdvancedProfiler::finish();

        $this->assertEquals(
            sprintf(
                "%s#%s",
                __FILE__,
                31
            ),
            $result[AdvancedProfiler::START_LABEL]
        );

        $this->assertEquals(
            sprintf(
                "%s#%s",
                __FILE__,
                32
            ),
            $result[AdvancedProfiler::FINISH_LABEL]
        );
    }
}
