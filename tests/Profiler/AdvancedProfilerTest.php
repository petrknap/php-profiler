<?php

use PetrKnap\Php\Profiler\AdvancedProfiler;
use PetrKnap\Php\Profiler\Profile;

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
                23
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
                32
            ),
            $result->meta[AdvancedProfiler::START_LABEL]
        );

        $this->assertEquals(
            sprintf(
                "%s#%s",
                __FILE__,
                33
            ),
            $result->meta[AdvancedProfiler::FINISH_LABEL]
        );
    }

    public function testPostProcessorSupport()
    {
        $postProcessorCallsCounter = 0;
        AdvancedProfiler::setPostProcessor(
            function ($result) use (&$postProcessorCallsCounter) {
                $postProcessorCallsCounter++;

                $this->assertInstanceOf(get_class(new Profile()), $result);

                $result->meta["post_processors_note"] = "note";

                return $result;
            }
        );

        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals($i, $postProcessorCallsCounter);

            AdvancedProfiler::start();
            $result = AdvancedProfiler::finish();

            $this->assertArrayHasKey("post_processors_note", $result->meta);
            $this->assertEquals("note", $result->meta["post_processors_note"]);

            $this->assertEquals($i + 1, $postProcessorCallsCounter);
        }
    }

    public function testPerformanceIsNotIntrusive()
    {
        $start = microtime(true);

        AdvancedProfiler::start();

        $diff = microtime(true) - $start;

        $this->assertLessThanOrEqual(0.001, $diff);


        $start = microtime(true);

        AdvancedProfiler::finish();

        $diff = microtime(true) - $start;

        $this->assertLessThanOrEqual(0.001, $diff);


        $start = microtime(true);

        AdvancedProfiler::start();
        AdvancedProfiler::finish();

        $diff = microtime(true) - $start;

        $this->assertLessThanOrEqual(0.002, $diff);
    }
}
