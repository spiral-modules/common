<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

use Psr\Log\LoggerInterface;

/**
 * Has to provide configured instance of log.
 */
interface LogsInterface
{
    /**
     * Get pre-configured logger instance.
     *
     * @param string $channel
     *
     * @return LoggerInterface
     */
    public function getLogger(string $channel): LoggerInterface;
}
