<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Traits;

use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerInterface;
use Spiral\Core\Exceptions\ScopeException;

/**
 * Saturate optional constructor or method argument (class) using internal (usually static)
 * container. In most of cases trait is doing nothing since spiral Container populates even
 * optional class dependencies.
 *
 * Avoid using this trait in custom code, it's only a development sugar.
 */
trait SaturateTrait
{
    /**
     * Must be used only to resolve optional constructor arguments. Use in classes which are
     * generally resolved using Container. Default value MUST always be supplied from outside.
     *
     * @param mixed  $default Default value.
     * @param string $class   Requested class.
     *
     * @return mixed|null|object
     *
     * @throws ScopeException
     */
    private function saturate($default, string $class)
    {
        if (!empty($default)) {
            return $default;
        }

        $container = $this->iocContainer();

        if (empty($container)) {
            throw new ScopeException("Unable to saturate '{$class}': no container available");
        }

        //Only when global container is set
        try {
            return $container->get($class);
        } catch (ContainerException $e) {
            throw new ScopeException("Unable to saturate '{$class}': {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Class specific container.
     *
     * @return ContainerInterface
     */
    abstract protected function iocContainer(): ?ContainerInterface;
}
