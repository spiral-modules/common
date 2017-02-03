<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Debug;

use Interop\Container\ContainerInterface;
use Mockery as m;
use Spiral\Core\Component;
use Spiral\Debug\Benchmarker;
use Spiral\Debug\BenchmarkerInterface;
use Spiral\Debug\Traits\BenchmarkTrait;
use Spiral\Tests\Core\Fixtures\SharedComponent;

class BenchmarkTraitTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        SharedComponent::shareContainer(null);
    }

    public function testNoBenchmarker()
    {
        $class = new BenchmarkedClass();
        $this->assertFalse($class->doAbc('test'));
    }

    public function testBenchmarker()
    {
        $benchmarker = m::mock(BenchmarkerInterface::class);

        $class = new BenchmarkedClass();
        $class->setBenchmarker($benchmarker);

        $benchmarker->shouldReceive('benchmark')->with(
            $class, 'record', 'test'
        )->andReturn(['payload']);

        $benchmarker->shouldReceive('benchmark')->with(
            $class, ['payload'], ''
        )->andReturn(0.01);

        $this->assertSame(0.01, $class->doAbc('test'));
    }

    public function testBenchmarkerThoughtContainer()
    {
        $container = m::mock(ContainerInterface::class);
        $container->shouldReceive('has')->with(BenchmarkerInterface::class)->andReturn(true);
        $container->shouldReceive('get')->with(BenchmarkerInterface::class)->andReturn(
            $benchmarker = m::mock(BenchmarkerInterface::class)
        );

        SharedComponent::shareContainer($container);

        $class = new BenchmarkedClass();

        $benchmarker->shouldReceive('benchmark')->with(
            $class, 'record', 'test'
        )->andReturn(['payload']);

        $benchmarker->shouldReceive('benchmark')->with(
            $class, ['payload'], ''
        )->andReturn(0.02);

        $this->assertSame(0.02, $class->doAbc('test'));
    }

    /**
     * @expectedException \Spiral\Debug\Exceptions\BenchmarkException
     * @expectedExceptionMessage Unpaired benchmark record 'invalid'
     */
    public function testUnpaired()
    {
        $b = new Benchmarker();
        $b->benchmark($this, [0 => 'invalid']);
    }
}

class BenchmarkedClass extends Component
{
    use BenchmarkTrait;

    public function doAbc($context)
    {
        $benchmark = $this->benchmark('record', $context);

        return $this->benchmark($benchmark);
    }
}