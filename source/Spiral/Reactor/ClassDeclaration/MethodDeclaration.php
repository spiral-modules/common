<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor\ClassDeclaration;

use Spiral\Reactor\Body\Source;
use Spiral\Reactor\ClassDeclaration\Aggregators\ParameterAggregator;
use Spiral\Reactor\Prototypes\NamedDeclaration;
use Spiral\Reactor\ReplaceableInterface;
use Spiral\Reactor\Traits\AccessTrait;
use Spiral\Reactor\Traits\CommentTrait;

/**
 * Represent class method.
 */
class MethodDeclaration extends NamedDeclaration implements ReplaceableInterface
{
    use CommentTrait, AccessTrait;

    /**
     * @var bool
     */
    private $static = false;

    /**
     * @var ParameterAggregator
     */
    private $parameters = null;

    /**
     * @var Source
     */
    private $source = null;

    /**
     * @param string       $name
     * @param string|array $source
     * @param string       $comment
     */
    public function __construct(string $name, $source = '', string $comment = '')
    {
        parent::__construct($name);

        $this->parameters = new ParameterAggregator([]);

        $this->initSource($source);
        $this->initComment($comment);
    }

    /**
     * @param bool $static
     *
     * @return self
     */
    public function setStatic(bool $static = true): MethodDeclaration
    {
        $this->static = (bool)$static;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->static;
    }

    /**
     * Rename to getSource()?
     *
     * @return Source
     */
    public function getSource(): Source
    {
        return $this->source;
    }

    /**
     * Set method source.
     *
     * @param string|array $source
     *
     * @return self
     */
    public function setSource($source): MethodDeclaration
    {
        if (!empty($source)) {
            if (is_array($source)) {
                $this->source->setLines($source);
            } elseif (is_string($source)) {
                $this->source->setString($source);
            }
        }

        return $this;
    }

    /**
     * @return ParameterAggregator|ParameterDeclaration[]
     */
    public function getParameters(): ParameterAggregator
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     *
     * @return ParameterDeclaration
     */
    public function parameter(string $name): ParameterDeclaration
    {
        return $this->parameters->get($name);
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function replace($search, $replace): MethodDeclaration
    {
        $this->docComment->replace($search, $replace);

        return $this;
    }

    /**
     * @param int $indentLevel
     *
     * @return string
     */
    public function render(int $indentLevel = 0): string
    {
        $result = '';
        if (!$this->docComment->isEmpty()) {
            $result .= $this->docComment->render($indentLevel) . "\n";
        }

        $method = "{$this->getAccess()} function {$this->getName()}";
        if (!$this->parameters->isEmpty()) {
            $method .= "({$this->parameters->render()})";
        } else {
            $method .= "()";
        }

        $result .= $this->addIndent($method, $indentLevel) . "\n";
        $result .= $this->addIndent('{', $indentLevel) . "\n";

        if (!$this->source->isEmpty()) {
            $result .= $this->source->render($indentLevel + 1) . "\n";
        }

        $result .= $this->addIndent("}", $indentLevel);

        return $result;
    }

    /**
     * Init source value.
     *
     * @param string|array $source
     */
    private function initSource($source)
    {
        if (empty($this->source)) {
            $this->source = new Source();
        }

        if (!empty($source)) {
            if (is_array($source)) {
                $this->source->setLines($source);
            } elseif (is_string($source)) {
                $this->source->setString($source);
            }
        }
    }
}