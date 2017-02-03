<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Debug;

use Spiral\Debug\Benchmarker;

class BenchmarkerTest extends \PHPUnit_Framework_TestCase
{
    public function testFlow()
    {
        $benchmarker = new Benchmarker();

        $bench = $benchmarker->benchmark($this, 'record');
        $this->assertInternalType('array', $bench);

        $result = $benchmarker->benchmark($this, $bench);
        $this->assertInternalType('float', $result);

        $this->assertCount(1, $benchmarker->getBenchmarks());
    }
}