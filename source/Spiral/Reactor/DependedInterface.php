<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor;

/**
 * Declares needed uses and aliases in array form.
 *
 * @todo automatically associate with class declaration?
 */
interface DependedInterface
{
    /**
     * Must return needed uses in array form [class => alias|null] to be automatically merged
     * with existed import set.
     *
     * @return array
     */
    public function getDependencies(): array;
}