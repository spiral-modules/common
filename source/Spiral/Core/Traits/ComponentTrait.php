<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Traits;

use Psr\Container\ContainerInterface;
use Spiral\Core\Component;

trait ComponentTrait
{
    /**
     * Get instance of container associated with given object or container available in global
     * scope.
     *
     * @return ContainerInterface|null
     */
    public function iocContainer(): ?ContainerInterface
    {
        return Component::staticContainer();
    }
}