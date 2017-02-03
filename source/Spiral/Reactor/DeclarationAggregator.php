<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor;

use Spiral\Reactor\Exceptions\ReactorException;
use Spiral\Reactor\Prototypes\Declaration;
use Spiral\Reactor\Prototypes\NamedDeclaration;

/**
 * Provides ability to aggregate specific set of elements (type constrained), render them or
 * apply set of operations.
 */
class DeclarationAggregator extends Declaration implements
    \ArrayAccess,
    \IteratorAggregate,
    ReplaceableInterface
{
    /**
     * @var array
     */
    private $allowed = [];

    /**
     * @var DeclarationInterface[]
     */
    private $elements = [];

    /**
     * @param array $allowed
     * @param array $elements
     */
    public function __construct(array $allowed, array $elements = [])
    {
        $this->allowed = $allowed;
        $this->elements = $elements;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * Check if aggregation has named element with given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        foreach ($this->elements as $element) {
            if ($element instanceof NamedDeclaration && $element->getName() == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add new element.
     *
     * @param DeclarationInterface $element
     *
     * @return self
     * @throws ReactorException
     */
    public function add(DeclarationInterface $element): DeclarationAggregator
    {
        $reflector = new \ReflectionObject($element);

        $allowed = false;
        foreach ($this->allowed as $class) {
            if ($reflector->isSubclassOf($class) || get_class($element) == $class) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            $type = get_class($element);
            throw new ReactorException("Elements with type '{$type}' are not allowed");
        }

        $this->elements[] = $element;

        return $this;
    }

    /**
     * Get named element by it's name.
     *
     * @param string $name
     *
     * @return DeclarationInterface
     *
     * @throws ReactorException
     */
    public function get(string $name)
    {
        if (!$this->has($name)) {
            throw new ReactorException("Undefined element '{$name}'");
        }

        return $this->find($name);
    }

    /**
     * Remove element by it's name.
     *
     * @param string $name
     *
     * @return self
     */
    public function remove(string $name): DeclarationAggregator
    {
        foreach ($this->elements as $index => $element) {
            if ($element instanceof NamedDeclaration && $element->getName() == $name) {
                unset($this->elements[$index]);
            }
        }

        return $this;
    }

    /**
     * Get element by it's name.
     *
     * @param string $name
     *
     * @return DeclarationInterface
     *
     * @throws ReactorException
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->remove($offset)->add($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public function replace($search, $replace): DeclarationAggregator
    {
        foreach ($this->elements as $element) {
            if ($element instanceof ReplaceableInterface) {
                $element->replace($search, $replace);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render(int $indentLevel = 0): string
    {
        $result = '';

        foreach ($this->elements as $element) {
            $result .= $element->render($indentLevel) . "\n\n";
        }

        return rtrim($result, "\n");
    }

    /**
     * Find element by it's name (NamedDeclarations only).
     *
     * @param string $name
     *
     * @return DeclarationInterface
     *
     * @throws ReactorException When unable to find.
     */
    protected function find(string $name)
    {
        foreach ($this->elements as $element) {
            if ($element instanceof NamedDeclaration && $element->getName() == $name) {
                return $element;
            }
        }

        throw new ReactorException("Unable to find element '{$name}'");
    }
}