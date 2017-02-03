<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

/**
 * Long memory cache. Something very fast on read and slow on write!
 */
interface MemoryInterface
{
    /**
     * Read data from long memory cache. Must return exacts same value as saved or null.
     *
     * @param string $section Non case sensitive.
     *
     * @return string|array|null
     */
    public function loadData(string $section);

    /**
     * Put data to long memory cache. No inner references or closures are allowed.
     *
     * @param string       $section Non case sensitive.
     * @param string|array $data
     */
    public function saveData(string $section, $data);
}
