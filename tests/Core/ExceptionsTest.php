<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Core;

use Spiral\Core\Container;
use Spiral\Core\Exceptions\Container\ArgumentException;
use Spiral\Core\Exceptions\Container\AutowireException;
use Spiral\Core\Exceptions\Container\ContainerException;
use Spiral\Core\Exceptions\DependencyException;

class ExceptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Spiral\Core\Exceptions\Container\ContainerException
     * @expectedExceptionMessage Invalid binding for 'invalid'
     */
    public function testInvalidBinding()
    {
        $container = new Container();
        $container->bind('invalid', ['invalid']);
        $container->get('invalid');
    }

    /**
     * @expectedException \Spiral\Core\Exceptions\Container\ContainerException
     * @expectedExceptionMessage Class Spiral\Tests\Core\InvalidClass does not exist
     */
    public function testInvalidInjectionParameter()
    {
        $container = new Container();

        $container->resolveArguments(new \ReflectionMethod($this, 'invalidInjection'));
    }

    public function testArgumentException(string $param = null)
    {
        $method = new \ReflectionMethod($this, 'testArgumentException');

        $e = new ArgumentException(
            $method->getParameters()[0],
            $method
        );

        $this->assertInstanceOf(AutowireException::class, $e);
        $this->assertInstanceOf(ContainerException::class, $e);
        $this->assertInstanceOf(DependencyException::class, $e);
        $this->assertInstanceOf(\Interop\Container\Exception\ContainerException::class, $e);

        $this->assertSame($method, $e->getContext());
        $this->assertSame('param', $e->getParameter()->getName());
    }

    protected function invalidInjection(InvalidClass $class)
    {

    }
}