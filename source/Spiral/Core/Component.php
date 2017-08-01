<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Psr\Container\ContainerInterface;

/**
 * Basic spiral cell. Automatically detects if "container" property are presented in class or uses
 * global container as fallback.
 */
abstract class Component
{
    /**
     * Global/static mainly used to resolve singletons outside of the runtime scope.
     * Must be used as fallback only, or not used at all. All spiral components can
     * behave well without it.
     *
     * @var ContainerInterface
     */
    private static $staticContainer = null;

    /**
     * Get instance of container associated with given object or container available in global
     * scope.
     *
     * @return ContainerInterface|null
     */
    protected function iocContainer(): ?ContainerInterface
    {
        if (
            property_exists($this, 'container')
            && isset($this->container)
            && $this->container instanceof ContainerInterface
        ) {
            return $this->container;
        }

        return self::$staticContainer;
    }

    /**
     * Global container scope access.
     *
     * @param ContainerInterface $scope Container to be used to set/replace existed scope.
     *
     * @return ContainerInterface|null
     */
    final public static function staticContainer(
        ContainerInterface $scope = null
    ): ?ContainerInterface
    {
        if (func_num_args() === 0) {
            return self::$staticContainer;
        }

        //Exchanging values
        $outer = self::$staticContainer;
        self::$staticContainer = $scope;

        //Return previous container or null
        return $outer;
    }
}
