<?php
/**
 * components
 *
 * @author    Wolfy-J
 */

namespace Spiral\Tests\Models;

use PHPUnit\Framework\TestCase;
use Spiral\Models\Traits\SolidableTrait;

class SolidTraitTest extends TestCase
{
    use SolidableTrait;

    public function testSolid()
    {
        $this->assertFalse($this->isSolid());
        $this->solidState(true);
        $this->assertTrue($this->isSolid());
        $this->solidState(false);
        $this->assertFalse($this->isSolid());

        $this->solidState();
        $this->assertTrue($this->isSolid());
    }
}