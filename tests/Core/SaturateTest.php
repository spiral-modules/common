<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Core;

use Interop\Container\ContainerInterface;
use Mockery as m;
use Spiral\Core\Container;
use Spiral\Core\Exceptions\Container\AutowireException;
use Spiral\Tests\Core\Fixtures\SampleClass;
use Spiral\Tests\Core\Fixtures\SaturableComponent;
use Spiral\Tests\Core\Fixtures\SharedComponent;

class SaturateTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        SharedComponent::shareContainer(null);
    }

    public function testManualClass()
    {
        $sample = new SampleClass();
        $saturable = new SaturableComponent($sample);

        $this->assertSame($sample, $saturable->getSample());
    }

    /**
     * @expectedException \Spiral\Core\Exceptions\ScopeException
     * @expectedExceptionMessage Unable to saturate 'Spiral\Tests\Core\Fixtures\SampleClass': no
     *                           container available
     */
    public function testMissingContainer()
    {
        $saturable = new SaturableComponent();
    }

    public function testSaturation()
    {
        $container = m::mock(ContainerInterface::class);

        SharedComponent::shareContainer($container);

        $container->shouldReceive('get')->with(SampleClass::class)->andReturn(
            $sample = new SampleClass()
        );

        $saturable = new SaturableComponent();
        $this->assertSame($sample, $saturable->getSample());
    }

    /**
     * @expectedException \Spiral\Core\Exceptions\ScopeException
     * @expectedExceptionMessage Unable to saturate 'Spiral\Tests\Core\Fixtures\SampleClass':
     *                           unable to create SampleClass
     */
    public function testFailedSaturation()
    {
        $container = m::mock(ContainerInterface::class);

        SharedComponent::shareContainer($container);

        $container->shouldReceive('get')->with(SampleClass::class)->andThrow(
            AutowireException::class,
            'unable to create SampleClass'
        );

        $saturable = new SaturableComponent();
    }

    public function testContainerSaturation()
    {
        $container = new Container();
        $container->bind(SampleClass::class, $sample = new SampleClass());

        $saturable = $container->get(SaturableComponent::class);
        $this->assertSame($sample, $saturable->getSample());
    }
}
