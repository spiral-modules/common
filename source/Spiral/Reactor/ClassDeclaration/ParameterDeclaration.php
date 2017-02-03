<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor\ClassDeclaration;

use Doctrine\Common\Inflector\Inflector;
use Spiral\Reactor\Prototypes\NamedDeclaration;
use Spiral\Reactor\Traits\SerializerTrait;

/**
 * Single method parameter.
 */
class ParameterDeclaration extends NamedDeclaration
{
    /**
     *
     */
    use SerializerTrait;

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var bool
     */
    private $isOptional = false;

    /**
     * @var mixed
     */
    private $defaultValue = null;

    /**
     * Passed by reference flag.
     *
     * @var bool
     */
    private $pdb = false;

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): ParameterDeclaration
    {
        return parent::setName(Inflector::camelize($name));
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): ParameterDeclaration
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Flag that parameter should pass by reference.
     *
     * @param bool $passedByReference
     *
     * @return self
     */
    public function setPBR(bool $passedByReference = false): ParameterDeclaration
    {
        $this->pdb = $passedByReference;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPBR(): bool
    {
        return $this->pdb;
    }

    /**
     * Check if parameter is optional.
     *
     * @return bool
     */
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * Set parameter default value.
     *
     * @param mixed $defaultValue
     *
     * @return self
     */
    public function setDefault($defaultValue): ParameterDeclaration
    {
        $this->isOptional = true;
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->defaultValue;
    }

    /**
     * Remove default value.
     *
     * @return self
     */
    public function removeDefault(): ParameterDeclaration
    {
        $this->isOptional = false;
        $this->defaultValue = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render(int $indentLevel = 0): string
    {
        $type = '';
        if (!empty($this->type)) {
            $type = $this->type . " ";
        }

        $result = $type . ($this->pdb ? '&' : '') . "$" . $this->getName();

        if (!$this->isOptional) {
            return $result;
        }

        return $result . ' = ' . $this->getSerializer()->serialize($this->defaultValue);
    }
}