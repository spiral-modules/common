<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor\Traits;

/**
 * Provide ability to declared namespase uses.
 */
trait UsesTrait
{
    /**
     * @var array
     */
    private $uses = [];

    /**
     * @param string $class
     *
     * @return bool
     */
    public function hasUse($class)
    {
        $class = ltrim($class, '\\');

        return array_search($class, $this->uses) !== false;
    }

    /**
     * Declare uses in a form of array [class => alias|null]. Existed uses will be dropped.
     *
     * @param array $uses
     *
     * @return self
     */
    public function setUses(array $uses)
    {
        $this->uses = [];

        return $this->addUses($uses);
    }

    /**
     * Add additional set of uses.
     *
     * @param array $uses
     *
     * @return self
     */
    public function addUses(array $uses)
    {
        foreach ($uses as $class => $alias) {
            $this->addUse($class, $alias);
        }

        return $this;
    }

    /**
     * @param string $class
     * @param string $alias Optional.
     *
     * @return self
     */
    public function addUse($class, $alias = null)
    {
        $this->uses[ltrim($class, '\\')] = $alias;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public function removeUse($class)
    {
        unset($this->uses[ltrim($class, '\\')]);

        return $this;
    }

    /**
     * @return array
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * @param int $indentLevel
     *
     * @return string
     */
    private function renderUses(int $indentLevel = 0): string
    {
        $lines = [];
        foreach ($this->getUses() as $class => $alias) {
            $line = "use {$class}";

            if (!empty($alias)) {
                $line .= " as {$alias};";
            } else {
                $line .= ";";
            }

            $lines[] = $this->addIndent($line, $indentLevel);
        }

        return join("\n", $lines);
    }

    /**
     * @param string $string
     * @param int    $indent
     *
     * @return string
     */
    abstract protected function addIndent(string $string, int $indent = 0): string;
}