<?php

namespace PetrKnap\Php\Profiler\Test;

use PetrKnap\Php\Profiler\Profile;
use PetrKnap\Php\Profiler\SimpleProfiler;

class SimpleProfilerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        SimpleProfiler::enable();
    }

    private function checkResult($result, $startLabel, $finishLabel)
    {
        /** @var Profile $result */
        $this->assertInstanceOf(get_class(new Profile()), $result);

        $this->assertLessThanOrEqual($result->absoluteDuration, $result->duration);

        $this->assertEquals($startLabel, $result->meta[SimpleProfiler::START_LABEL]);
        $this->assertEquals($finishLabel, $result->meta[SimpleProfiler::FINISH_LABEL]);
    }

    public function testEmptyStack()
    {
        $this->setExpectedException(get_class(new \OutOfRangeException()));

        SimpleProfiler::finish();
    }

    public function testEnable()
    {
        SimpleProfiler::enable();

        $this->assertTrue(SimpleProfiler::start());
        $this->assertInstanceOf(get_class(new Profile()), SimpleProfiler::finish());
    }

    public function testDisable()
    {
        SimpleProfiler::disable();

        $this->assertFalse(SimpleProfiler::start());
        $this->assertFalse(SimpleProfiler::finish());
    }

    public function testOneLevelProfiling()
    {
        SimpleProfiler::start("start");

        $result = SimpleProfiler::finish("finish");

        $this->checkResult($result, "start", "finish");
    }

    public function testTwoLevelProfiling()
    {
        #region First level
        SimpleProfiler::start("L1_S");

        #region Second level A
        SimpleProfiler::start("L2A_S");

        $result = SimpleProfiler::finish("L2A_F");

        $this->checkResult($result, "L2A_S", "L2A_F");
        #endregion

        #region Second level B
        SimpleProfiler::start("L2B_S");

        $result = SimpleProfiler::finish("L2B_F");

        $this->checkResult($result, "L2B_S", "L2B_F");
        #endregion

        $result = SimpleProfiler::finish("L1_F");

        $this->checkResult($result, "L1_S", "L1_F");
        #endregion
    }

    public function testTimeProfiling()
    {
        #region First level (A)
        SimpleProfiler::start();

        sleep(1);

        #region Second level - first (B)
        SimpleProfiler::start();

        sleep(3);

        $B = SimpleProfiler::finish();
        #endregion

        #region Second level - second (C)
        SimpleProfiler::start();

        sleep(2);

        $C = SimpleProfiler::finish();
        #endregion

        $A = SimpleProfiler::finish();
        #endregion

        $this->assertEquals(6, $A->absoluteDuration, "", 0.5);
        $this->assertEquals(3, $B->absoluteDuration, "", 0.5);
        $this->assertEquals(2, $C->absoluteDuration, "", 0.5);

        $this->assertEquals(1, $A->duration, "", 0.5);
        $this->assertEquals(3, $B->duration, "", 0.5);
        $this->assertEquals(2, $C->duration, "", 0.5);
    }

    public function testMemoryProfiling()
    {
        SimpleProfiler::start();

        $largeObject = null;
        for ($i = 0; $i < 1000; $i++) {
            SimpleProfiler::start();
            $largeObject = new \Exception("Large object", 0, $largeObject);
            SimpleProfiler::finish();
        }

        $result = SimpleProfiler::finish();

        $this->assertGreaterThan($result->meta[SimpleProfiler::START_MEMORY_USAGE], $result->meta[SimpleProfiler::FINISH_MEMORY_USAGE]);
        $this->assertGreaterThan($result->memoryUsageChange, $result->absoluteMemoryUsageChange);
        $this->assertGreaterThan(0, $result->absoluteMemoryUsageChange);
        $this->assertGreaterThan(0, $result->memoryUsageChange);

        SimpleProfiler::start();

        unset($largeObject);

        $result = SimpleProfiler::finish();

        $this->assertLessThan($result->meta[SimpleProfiler::START_MEMORY_USAGE], $result->meta[SimpleProfiler::FINISH_MEMORY_USAGE]);
        $this->assertEquals($result->absoluteMemoryUsageChange, $result->memoryUsageChange);
        $this->assertLessThan(0, $result->absoluteMemoryUsageChange);
        $this->assertLessThan(0, $result->memoryUsageChange);
    }

    public function testPerformanceIsNotIntrusive()
    {
        $start = microtime(true);

        SimpleProfiler::start();

        $diff = microtime(true) - $start;

        $this->assertLessThanOrEqual(0.001, $diff);


        $start = microtime(true);

        SimpleProfiler::finish();

        $diff = microtime(true) - $start;

        $this->assertLessThanOrEqual(0.001, $diff);


        $start = microtime(true);

        SimpleProfiler::start();
        SimpleProfiler::finish();

        $diff = microtime(true) - $start;

        $this->assertLessThanOrEqual(0.002, $diff);
    }
}
