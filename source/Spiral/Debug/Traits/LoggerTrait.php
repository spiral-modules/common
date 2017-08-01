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

        return $this->logger = $this->makeLogger(static::class);
    }

    /**
     * Alias for "getLogger" function.
     *
     * @deprecated Use getLogger() instead.
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
     * @param string $channel
     *
     * @return LoggerInterface
     */
    private function makeLogger(string $channel): LoggerInterface
    {
        $container = $this->iocContainer();
        if (empty($container) || !$container->has(LogsInterface::class)) {
            return new NullLogger();
        }

        return $container->get(LogsInterface::class)->getLogger($channel);
    }
}
