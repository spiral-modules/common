<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Models;

use Spiral\Models\Exceptions\EntityExceptionInterface;

/**
 * Common interface to indicate that entity implements ActiveRecord pattern.
 */
interface ActiveEntityInterface
{
    const CREATED   = 1;
    const UPDATED   = 2;
    const UNCHANGED = 3;

    /**
     * Create entity or update entity state in database.
     *
     * @return int Must return one of constants self::CREATED, self::UPDATED, self::UNCHANGED
     * @throws EntityExceptionInterface
     */
    public function save(): int;

    /**
     * Delete entity from it's primary storage, entity object must not be used anymore after that
     * operation.
     */
    public function delete();
}