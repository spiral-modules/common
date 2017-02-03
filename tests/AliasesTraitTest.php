<?php
/**
 * components
 *
 * @author    Wolfy-J
 */

namespace Spiral\Tests\Core;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Traits\Config\AliasTrait;

class AliasesTraitTest extends TestCase
{
    use AliasTrait;

    protected $config = [
        'aliases' => [
            'a' => 'b'
        ]
    ];

    public function testAliases()
    {
        $this->assertSame('b', $this->resolveAlias('a'));
    }
}