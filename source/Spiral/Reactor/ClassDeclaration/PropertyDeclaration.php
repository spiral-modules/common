<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor\ClassDeclaration;

use Spiral\Reactor\Prototypes\NamedDeclaration;
use Spiral\Reactor\ReplaceableInterface;
use Spiral\Reactor\Traits\AccessTrait;
use Spiral\Reactor\Traits\CommentTrait;
use Spiral\Reactor\Traits\SerializerTrait;

/**
 * Declares property element.
 */
class PropertyDeclaration extends NamedDeclaration implements ReplaceableInterface
{
    use CommentTrait, SerializerTrait, AccessTrait;

    /**
     * @var bool
     */
    private $hasDefault = false;

    /**
     * @var mixed
     */
    private $defaultValue = null;

    /**
     * @param string $name
     * @param mixed  $defaultValue
     * @param string $comment
     */
    public function __construct(string $name, $defaultValue = null, string $comment = '')
    {
        parent::__construct($name);
        $this->setDefault($defaultValue);
        $this->initComment($comment);
    }

    /**
     * Has default value.
     *
     * @return bool
     */
    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    /**
     * Set default value.
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setDefault($value): PropertyDeclaration
    {
        $this->hasDefault = true;
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * Remove default value.
     *
     * @return self
     */
    public function removeDefault(): PropertyDeclaration
    {
        $this->hasDefault = false;
        $this->defaultValue = null;

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
     * Replace comments.
     *
     * @param array|string $search
     * @param array|string $replace
     */
    public function replace($search, $replace)
    {
        $this->docComment->replace($search, $replace);
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

        $result .= $this->addIndent("{$this->access} \${$this->getName()}", $indentLevel);

        if ($this->hasDefault) {
            $value = $this->getSerializer()->serialize($this->defaultValue);

            if (is_array($this->defaultValue)) {
                $value = $this->mountIndents($value, $indentLevel);
            }

            $result .= " = {$value};";
        } else {
            $result .= ";";
        }

        return $result;
    }

    /**
     * Mount indentation to value. Attention, to be applied to arrays only!
     *
     * @param string $serialized
     * @param int    $indentLevel
     *
     * @return string
     */
    private function mountIndents(string $serialized, int $indentLevel): string
    {
        $lines = explode("\n", $serialized);
        foreach ($lines as &$line) {
            $line = $this->addIndent($line, $indentLevel);
            unset($line);
        }

        return ltrim(join("\n", $lines));
    }
}