<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

/**
 * Interface responsible for benchmarking.
 *
 * @deprecated this implementation and future usages must be deprecated.
 */
interface BenchmarkerInterface
{
    /**
     * Benchmarks used to record long or important operations inside spiral components. Method must
     * return benchmark payload on first call and elapsed time on second. Benchmark payload must be
     * provided on first method call and be accepted instead on "record" argument on second.
     *
     * Example:
     * $payload = $benchmarker->benchmark($this, 'record');
     * ...
     * $elapsed = $benchmarker->benchmark($this, $payload);
     *
     * @param object $caller  Call initiator (used to de-group events).
     * @param string $record  Benchmark record name or payload.
     * @param string $context Record context (if any).
     *
     * @return bool|float|mixed
     */
    public function benchmark($caller, $record, string $context = '');
}
