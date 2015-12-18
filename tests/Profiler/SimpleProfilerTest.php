<?php

use PetrKnap\Php\Profiler\SimpleProfiler;

class SimpleProfilerTest extends PHPUnit_Framework_TestCase
{
    const ACCEPTABLE_DELAY = 0.002; // 2 ms

    public function setUp()
    {
        parent::setUp();

        SimpleProfiler::enable();
    }

    private function checkResult(array $result, $startLabel, $finishLabel)
    {
        $this->assertArrayHasKey(SimpleProfiler::START_LABEL, $result);
        $this->assertArrayHasKey(SimpleProfiler::START_TIME, $result);
        $this->assertArrayHasKey(SimpleProfiler::START_MEMORY_USAGE, $result);
        $this->assertArrayHasKey(SimpleProfiler::FINISH_LABEL, $result);
        $this->assertArrayHasKey(SimpleProfiler::FINISH_TIME, $result);
        $this->assertArrayHasKey(SimpleProfiler::FINISH_MEMORY_USAGE, $result);
        $this->assertArrayHasKey(SimpleProfiler::TIME_OFFSET, $result);
        $this->assertArrayHasKey(SimpleProfiler::ABSOLUTE_DURATION, $result);
        $this->assertArrayHasKey(SimpleProfiler::DURATION, $result);

        $this->assertLessThanOrEqual($result[SimpleProfiler::ABSOLUTE_DURATION], $result[SimpleProfiler::DURATION]);

        $this->assertEquals($startLabel, $result[SimpleProfiler::START_LABEL]);
        $this->assertEquals($finishLabel, $result[SimpleProfiler::FINISH_LABEL]);
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
        $this->assertTrue(is_array(SimpleProfiler::finish()));
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

        $this->assertEquals(6, $A[SimpleProfiler::ABSOLUTE_DURATION], "", self::ACCEPTABLE_DELAY);
        $this->assertEquals(3, $B[SimpleProfiler::ABSOLUTE_DURATION], "",  self::ACCEPTABLE_DELAY);
        $this->assertEquals(2, $C[SimpleProfiler::ABSOLUTE_DURATION], "",  self::ACCEPTABLE_DELAY);

        $this->assertEquals(1, $A[SimpleProfiler::DURATION], "", self::ACCEPTABLE_DELAY);
        $this->assertEquals(3, $B[SimpleProfiler::DURATION], "", self::ACCEPTABLE_DELAY);
        $this->assertEquals(2, $C[SimpleProfiler::DURATION], "", self::ACCEPTABLE_DELAY);
    }

    public function testMemoryProfiling()
    {
        SimpleProfiler::start();

        $largeObject = null;
        for($i = 0; $i < 1000; $i++) {
            SimpleProfiler::start();
            $largeObject = new \Exception("Large object", 0, $largeObject);
            SimpleProfiler::finish();
        }

        $result = SimpleProfiler::finish();

        $this->assertGreaterThan($result[SimpleProfiler::START_MEMORY_USAGE], $result[SimpleProfiler::FINISH_MEMORY_USAGE]);
        $this->assertGreaterThan($result[SimpleProfiler::MEMORY_USAGE_CHANGE], $result[SimpleProfiler::ABSOLUTE_MEMORY_USAGE_CHANGE]);
        $this->assertGreaterThan(0, $result[SimpleProfiler::ABSOLUTE_MEMORY_USAGE_CHANGE]);
        $this->assertGreaterThan(0, $result[SimpleProfiler::MEMORY_USAGE_CHANGE]);

        SimpleProfiler::start();

        unset($largeObject);

        $result = SimpleProfiler::finish();

        $this->assertLessThan($result[SimpleProfiler::START_MEMORY_USAGE], $result[SimpleProfiler::FINISH_MEMORY_USAGE]);
        $this->assertEquals($result[SimpleProfiler::ABSOLUTE_MEMORY_USAGE_CHANGE], $result[SimpleProfiler::MEMORY_USAGE_CHANGE]);
        $this->assertLessThan(0, $result[SimpleProfiler::ABSOLUTE_MEMORY_USAGE_CHANGE]);
        $this->assertLessThan(0, $result[SimpleProfiler::MEMORY_USAGE_CHANGE]);
    }
}
