<?php
/**
 * components
 *
 * @author    Wolfy-J
 */

namespace Spiral\Tests\Core;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;

class ContainerTest extends TestCase
{
    /**
     * @expectedException \Spiral\Core\Exceptions\LogicException
     */
    public function testClone()
    {
        $c = new Container();
        clone $c;
    }
}