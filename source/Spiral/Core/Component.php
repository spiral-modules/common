<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Interop\Container\ContainerInterface;

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
     * Get instance of container associated with given object, uses global container as fallback
     * if not. Method generally used by traits.
     *
     * @return ContainerInterface|null
     */
    protected function iocContainer()
    {
        if (
            property_exists($this, 'container')
            && isset($this->container)
            && $this->container instanceof ContainerInterface
        ) {
            return $this->container;
        }

        /*
         * Technically your code can work without ever being using fallback container, all
         * spiral components can do that and only few traits will require few extra parameters.
         *
         * I'm planning one day (update: DONE) to run spiral as worker and later in async mode (few app
         * processes in memory), memory schemas might help a lot by minimizing runtime code by using
         * pre-calculated behaviours.
         */

        return self::$staticContainer;
    }

    /**
     * Get/set instance of global/static container, due this method must be used as few times as
     * possible both getter and setter methods joined into one. Please use this method only as
     * fallback.
     *
     * @internal Do not use for business logic.
     *
     * @param ContainerInterface $container Can be set to null.
     *
     * @return ContainerInterface|null
     */
    final protected static function staticContainer(ContainerInterface $container = null)
    {
        if (func_num_args() === 0) {
            return self::$staticContainer;
        }

        //Exchanging values
        $outer = self::$staticContainer;
        self::$staticContainer = $container;

        //Return previous container or null
        return $outer;
    }
}
