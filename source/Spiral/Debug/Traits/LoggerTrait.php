<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Traits;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Spiral\Debug\LogsInterface;

/**
 * On demand logger creation. Allows class to share same logger between instances. Logger trait work
 * thought IoC scope!
 */
trait LoggerTrait
{
    /**
     * Private and null.
     *
     * @internal
     *
     * @var LoggerInterface|null
     */
    private $logger = null;

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get associated or create new instance of LoggerInterface.
     *
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        if (!empty($this->logger)) {
            return $this->logger;
        }

        //We are using class name as log channel (name) by default
        return $this->logger = $this->createLogger();
    }

    /**
     * Alias for "getLogger" function.
     *
     * @return LoggerInterface
     */
    protected function logger(): LoggerInterface
    {
        return $this->getLogger();
    }

    /**
     * @return ContainerInterface
     */
    abstract protected function iocContainer(): ?ContainerInterface;

    /**
     * Create new instance of associated logger (on demand creation).
     *
     * @return LoggerInterface
     */
    private function createLogger(): LoggerInterface
    {
        $container = $this->iocContainer();
        if (empty($container) || !$container->has(LogsInterface::class)) {
            return new NullLogger();
        }

        //We are using class name as log channel (name) by default
        return $container->get(LogsInterface::class)->getLogger(static::class);
    }
}
