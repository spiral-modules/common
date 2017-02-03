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
use Spiral\Reactor\Traits\CommentTrait;
use Spiral\Reactor\Traits\SerializerTrait;

/**
 * Class constant declaration.
 */
class ConstantDeclaration extends NamedDeclaration
{
    /**
     * Constants and properties.
     */
    use CommentTrait, SerializerTrait;

    /**
     * @var mixed
     */
    private $value = null;

    /**
     * @param string       $name
     * @param string       $value
     * @param string|array $comment
     */
    public function __construct($name, $value, $comment = '')
    {
        parent::__construct($name);
        $this->value = $value;
        $this->initComment($comment);
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): ConstantDeclaration
    {
        return parent::setName(
            strtoupper(Inflector::tableize(strtolower($name)))
        );
    }

    /**
     * Array values allowed (but works in PHP7 only).
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setValue($value): ConstantDeclaration
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function render(int $indentLevel = 0): string
    {
        $result = '';
        if (!$this->docComment->isEmpty()) {
            $result .= $this->docComment->render($indentLevel) . "\n";
        }

        $result .= $this->addIndent("const {$this->getName()} = ", $indentLevel);

        //todo: make indent level work
        return $result . $this->getSerializer()->serialize($this->value);
    }
}