<?php

/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Support;

class InterpolateTest extends \PHPUnit_Framework_TestCase
{
    //Base test to verify function is working
    /**
     * @covers \Spiral\interpolate
     */
    public function testInterpolate()
    {
        $result = \Spiral\interpolate("Hello {name}", ['name' => 'Anton']);
        $this->assertSame('Hello Anton', $result);
    }

    public function testInterpolateCustomBraces()
    {
        $result = \Spiral\interpolate("Hello [name]", ['name' => 'Anton'], '[', ']');
        $this->assertSame('Hello Anton', $result);
    }
}