<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Core;

use Spiral\Core\Container;
use Spiral\Tests\Core\Fixtures\OtherComponent;
use Spiral\Tests\Core\Fixtures\SharedComponent;

class ComponentTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        SharedComponent::shareContainer(null);
    }

    public function testShareContainer()
    {
        $containerA = new Container();
        $containerB = new Container();

        $this->assertNull(SharedComponent::shareContainer($containerA));
        $this->assertSame($containerA, SharedComponent::shareContainer($containerB));
        $this->assertSame($containerB, SharedComponent::shareContainer(null));
    }

    public function testMissingContainer()
    {
        $component = new SharedComponent();
        $this->assertNull($component->getContainer());
    }

    public function testFallbackContainer()
    {
        $sharedContainer = new Container();
        $this->assertNull(SharedComponent::shareContainer($sharedContainer));

        $component = new SharedComponent();
        $this->assertSame($sharedContainer, $component->getContainer());
    }

    public function testLocalContainer()
    {
        $sharedContainer = new Container();
        $this->assertNull(SharedComponent::shareContainer($sharedContainer));

        $localContainer = new Container();

        $component = new OtherComponent($localContainer);
        $this->assertSame($localContainer, $component->getContainer());

        $component = new SharedComponent();
        $this->assertSame($sharedContainer, $component->getContainer());
    }

    public function testSharedScope()
    {
        $containerA = new Container();
        $containerB = new Container();
        $component = new SharedComponent();

        $this->assertNull(SharedComponent::shareContainer($containerA));
        $this->assertSame($containerA, $component->getContainer());

        $this->assertSame($containerA, SharedComponent::shareContainer($containerB));
        $this->assertSame($containerB, $component->getContainer());
        $this->assertSame($containerB, SharedComponent::getShared());

        $this->assertSame($containerB, SharedComponent::shareContainer($containerA));
        $this->assertSame($containerA, $component->getContainer());
    }
}
