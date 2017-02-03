<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Spiral\Core\Container\InjectableInterface;

/**
 * Simple Spiral components config interface.
 */
interface ConfigInterface extends InjectableInterface, \ArrayAccess
{
    /**
     * Serialize config into array.
     *
     * @return array
     */
    public function toArray(): array;
}
