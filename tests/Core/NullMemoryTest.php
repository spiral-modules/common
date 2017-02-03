<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Spiral\Core;

use Spiral\Core\MemoryInterface;
use Spiral\Core\NullMemory;

class NullMemoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadData()
    {
        $memory = new NullMemory();
        $this->assertInstanceOf(MemoryInterface::class, $memory);
        $this->assertNull($memory->loadData('test'));
    }

    public function testSaveData()
    {
        $memory = new NullMemory();
        $this->assertInstanceOf(MemoryInterface::class, $memory);
        $memory->saveData('test', null);
    }
}