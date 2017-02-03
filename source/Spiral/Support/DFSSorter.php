<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Lev Seleznev
 */

namespace Spiral\Support;

/**
 * Topological Sorting vs Depth First Traversal (DFS)
 * https://en.wikipedia.org/wiki/Topological_sorting.
 */
class DFSSorter
{
    const STATE_NEW    = 1;
    const STATE_PASSED = 2;

    /**
     * @var array string[]
     */
    private $keys = [];

    /**
     * @var array
     */
    private $states = [];

    /**
     * @var array mixed[]
     */
    private $stack = [];

    /**
     * @var array mixed[]
     */
    private $objects = [];

    /**
     * @var array mixed[]
     */
    private $dependencies = [];

    /**
     * @param string $key          Item key, has to be used as reference in dependencies.
     * @param mixed  $item
     * @param array  $dependencies Must include keys object depends on.
     *
     * @return self
     */
    public function addItem(string $key, $item, array $dependencies): DFSSorter
    {
        $this->keys[] = $key;
        $this->objects[$key] = $item;
        $this->dependencies[$key] = $dependencies;

        return $this;
    }

    /**
     * Return sorted stack.
     *
     * @return array
     */
    public function sort(): array
    {
        $items = array_values($this->keys);

        $this->states = $this->stack = [];
        foreach ($items as $item) {
            $this->dfs($item, $this->dependencies[$item]);
        }

        return $this->stack;
    }

    /**
     * @param string $key
     * @param array  $dependencies
     */
    private function dfs(string $key, array $dependencies)
    {
        if (isset($this->states[$key])) {
            return;
        }

        $this->states[$key] = self::STATE_NEW;
        foreach ($dependencies as $dependency) {
            $this->dfs($dependency, $this->dependencies[$dependency]);
        }

        $this->stack[] = $this->objects[$key];
        $this->states[$key] = self::STATE_PASSED;

        return;
    }
}
