<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Traits;

use Interop\Container\ContainerInterface;
use Spiral\Debug\BenchmarkerInterface;

/**
 * Provides access to benchmark function.
 */
trait BenchmarkTrait
{
    /**
     * @invisible
     *
     * @var BenchmarkerInterface
     */
    private $benchmarker;

    /**
     * Set custom benchmarker.
     *
     * @param BenchmarkerInterface $benchmarker
     */
    public function setBenchmarker(BenchmarkerInterface $benchmarker)
    {
        $this->benchmarker = $benchmarker;
    }

    /**
     * Benchmarks used to record long or important operations inside spiral components. Method
     * should return elapsed time when record are be closed (same set of arguments has to be
     * provided).
     *
     * @param string|array $record  Benchmark record name or set of names.
     * @param string       $context Record context (if any).
     *
     * @return bool|float|mixed
     */
    private function benchmark($record, string $context = '')
    {
        if (empty($this->benchmarker)) {
            $container = $this->iocContainer();

            if (empty($container) || !$container->has(BenchmarkerInterface::class)) {
                //Nothing to do
                return false;
            }

            $this->benchmarker = $container->get(BenchmarkerInterface::class);
        }

        return $this->benchmarker->benchmark($this, $record, $context);
    }

    /**
     * @return ContainerInterface
     */
    abstract protected function iocContainer();
}
