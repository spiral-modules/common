<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Models;

/**
 * Declares ability to share only part of it's fields in a form of public data (access limitation).
 */
interface PublishableInterface
{
    /**
     * Shares only part of it's fields.
     *
     * @return array
     */
    public function publicValue(): array;
}