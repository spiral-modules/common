<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tests\Core\Fixtures;

use Spiral\Core\Component;
use Spiral\Core\Traits\SaturateTrait;

class SaturableComponent extends Component
{
    use SaturateTrait;

    /**
     * @var SampleClass
     */
    private $sample;

    /**
     * @param SampleClass|null $sample
     */
    public function __construct(SampleClass $sample = null)
    {
        $this->sample = $this->saturate($sample, SampleClass::class);
    }

    /**
     * @return SampleClass
     */
    public function getSample()
    {
        return $this->sample;
    }
}
