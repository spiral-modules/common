<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Support;

use Spiral\Reactor\Body\Source;
use Spiral\Reactor\Traits\SerializerTrait;
use Spiral\Support\Serializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    //To cover this weird trait as well
    use SerializerTrait;

    public function setUp()
    {
        $this->setSerializer(new Serializer());
    }

    public function testEmptyArray()
    {
        $this->assertSame('[]', $this->getSerializer()->serialize([]));
    }

    public function testArrayOfArray()
    {
        $this->assertEquals(preg_replace('/\s+/', '',
            '[
    \'hello\' => [
        \'name\' => 123
    ]
]'), preg_replace('/\s+/', '', $this->getSerializer()->serialize([
            'hello' => ['name' => 123]
        ])));
    }

    public function testArrayOfArray2()
    {
        $this->assertEquals(preg_replace('/\s+/', '',
            '[
    \'hello\' => [
        \'name\' => 123,
        \'sub\'  => magic
    ]
]'), preg_replace('/\s+/', '', $this->getSerializer()->serialize([
            'hello' => ['name' => 123, 'sub' => new Source(['magic'])]
        ])));
    }

    public function testClassNames()
    {
        $this->assertEquals(preg_replace('/\s+/', '',
            '[
    \'hello\' => [
        \'name\' => 123,
        \'sub\'  => \Spiral\Support\Serializer::class
    ]
]'), preg_replace('/\s+/', '', $this->getSerializer()->serialize([
            'hello' => ['name' => 123, 'sub' => Serializer::class]
        ])));
    }
}