<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Models\Traits;

/**
 * Provides ability for object (entity or accessor) to indicate that it's values must always be
 * stored together (no dirty fields, no atomic operations).
 *
 * Note: it's not the same as solid state relay :)
 */
trait SolidableTrait
{
    /**
     * SolidState MUST force object to be saved as one big dataset without any atomic operations
     * or dirty field processing.
     *
     * @var bool
     */
    private $solidState = false;

    /**
     * Declare object as solid.
     *
     * @param bool $solidState
     *
     * @return $this
     */
    public function solidState(bool $solidState = true)
    {
        $this->solidState = $solidState;

        return $this;
    }

    /**
     * Is object is solid state?
     *
     * @see solidState()
     *
     * @return bool
     */
    public function isSolid()
    {
        return $this->solidState;
    }
}