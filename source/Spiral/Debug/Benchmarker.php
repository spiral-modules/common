<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

use Spiral\Core\Component;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Debug\Exceptions\BenchmarkException;

/**
 * Generic benchmarker.
 *
 * @todo improve implementation by returning Benchmark?
 */
class Benchmarker extends Component implements BenchmarkerInterface, SingletonInterface
{
    /**
     * @invisible
     *
     * @var array
     */
    private $benchmarks = [];

    /**
     * {@inheritdoc}
     *
     * @throws BenchmarkException
     */
    public function benchmark($caller, $record, string $context = '')
    {
        $benchmarkID = count($this->benchmarks);
        if (is_array($record)) {
            $benchmarkID = $record[0];
        } elseif (!isset($this->benchmarks[$benchmarkID])) {
            $this->benchmarks[$benchmarkID] = [$caller, $record, $context, microtime(true)];

            //Payload
            return [$benchmarkID];
        }

        if (!isset($this->benchmarks[$benchmarkID])) {
            throw new BenchmarkException("Unpaired benchmark record '{$benchmarkID}'");
        }

        $this->benchmarks[$benchmarkID][4] = microtime(true);

        return $this->benchmarks[$benchmarkID][4] - $this->benchmarks[$benchmarkID][3];
    }

    /**
     * Retrieve all active and finished benchmark records.
     *
     * @return array
     */
    public function getBenchmarks(): array
    {
        return $this->benchmarks;
    }
}
