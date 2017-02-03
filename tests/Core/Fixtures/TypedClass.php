<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Core\Fixtures;

class TypedClass
{
    public function __construct(
        string $string,
        int $int,
        float $float,
        bool $bool,
        array $array = [],
        string $pong = null
    ) {
    }
}