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

    public function testPostProcessorSupport()
    {
        $postProcessorCallsCounter = 0;
        AdvancedProfiler::setPostProcessor(
            function ($result) use (&$postProcessorCallsCounter) {
                $postProcessorCallsCounter++;
                $this->assertTrue(is_array($result));

                $result["post_processors_note"] = "note";

                return $result;
            }
        );

        for ($i = 0; $i < 10; $i++) {
            $this->assertEquals($i, $postProcessorCallsCounter);

            AdvancedProfiler::start();
            $result = AdvancedProfiler::finish();

            $this->assertArrayHasKey("post_processors_note", $result);
            $this->assertEquals("note", $result["post_processors_note"]);

            $this->assertEquals($i + 1, $postProcessorCallsCounter);
        }
    }
}
