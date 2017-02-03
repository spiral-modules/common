<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Exceptions\Container;

use Spiral\Core\Exceptions\DependencyException;

/**
 * Something inside container.
 */
class ContainerException extends DependencyException implements \Interop\Container\Exception\ContainerException
{
}
