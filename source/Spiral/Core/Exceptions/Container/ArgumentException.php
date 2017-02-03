<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core\Exceptions\Container;

/**
 * Unable to resolve argument value.
 */
class ArgumentException extends AutowireException
{
    /**
     * Parameter caused error.
     *
     * @var \ReflectionParameter
     */
    protected $parameter = null;

    /**
     * Context method or constructor or function.
     *
     * @var \ReflectionFunctionAbstract
     */
    protected $context = null;

    /**
     * @param \ReflectionParameter        $parameter
     * @param \ReflectionFunctionAbstract $context
     */
    public function __construct(
        \ReflectionParameter $parameter,
        \ReflectionFunctionAbstract $context
    ) {
        $this->parameter = $parameter;
        $this->context = $context;

        $name = $context->getName();
        if ($context instanceof \ReflectionMethod) {
            $name = $context->class . '::' . $name;
        }

        parent::__construct("Unable to resolve '{$parameter->name}' argument in '{$name}'");
    }

    /**
     * @return \ReflectionParameter
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @return \ReflectionFunctionAbstract
     */
    public function getContext()
    {
        return $this->context;
    }
}
