<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Core;

use Spiral\Core\Exceptions\ConfigException;

/**
 * Generic implementation of array based configuration.
 *
 * Attention! Config has to be serialiable and be depended ONLY on environment or runtime
 * modifications/requests. No custom logic is allowed to initiate config, in other case config cache
 * will be invalid (pss... there is no config cache anymore).
 */
class InjectableConfig extends Component implements ConfigInterface, \IteratorAggregate
{
    /**
     * Spiral provides ability to automatically inject configs using configurator.
     */
    const INJECTOR = ConfiguratorInterface::class;

    /**
     * Configuration data.
     *
     * @var array
     */
    protected $config = [];

    /**
     * At this moment on array based configs can be supported.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new ConfigException("Undefined configuration key '{$offset}'");
        }

        return $this->config[$offset];
    }

    /**
     *{@inheritdoc}
     *
     * @throws ConfigException
     */
    public function offsetSet($offset, $value)
    {
        throw new ConfigException(
            'Unable to change configuration data, configs are treated as immutable by default'
        );
    }

    /**
     *{@inheritdoc}
     *
     * @throws ConfigException
     */
    public function offsetUnset($offset)
    {
        throw new ConfigException(
            'Unable to change configuration data, configs are treated as immutable by default'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->config);
    }

    /**
     * Restoring state.
     *
     * @param array $an_array
     *
     * @return static
     */
    public static function __set_state($an_array)
    {
        return new static($an_array['config']);
    }
}
