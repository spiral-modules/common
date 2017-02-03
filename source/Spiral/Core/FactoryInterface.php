<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Spiral\Core\Exceptions\Container\ArgumentException;
use Spiral\Core\Exceptions\Container\AutowireException;

/**
 * Declares ability to construct classes.
 *
 * @attention At this moment construct method has been splited from ContainerInterface, docs has
 *            to be updated.
 */
interface FactoryInterface
{
    /**
     * Create instance of requested class using binding class aliases and set of parameters provided
     * by user, rest of constructor parameters must be filled by container. Method might return
     * pre-constructed singleton!
     *
     * @param string $class
     * @param array  $parameters Parameters to construct new class.
     *
     * @return mixed|null|object
     *
     * @throws AutowireException
     * @throws ArgumentException
     */
    public function make(string $class, $parameters = []);
}
