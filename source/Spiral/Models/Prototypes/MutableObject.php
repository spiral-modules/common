<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Models\Prototypes;

use Spiral\Core\Component;

/**
 * Entity with ability to alter it's behaviour using set of statically assigned events.
 *
 * You can create special set of functions inside this class which are going to be automatically
 * initialized on class level.
 *
 * Simply define method with name: __init__... you can get access to self::events() inside it.
 * Traits can be used to define such methods.
 */
abstract class MutableObject extends Component
{
    /**
     * Every entity might have set of traits which can be initiated manually or at moment of
     * construction model instance. Array will store already initiated model names.
     *
     * @var array
     */
    private static $initiated = [];

    /**
     * Clear initiated objects list.
     */
    public static function resetInitiated()
    {
        self::$initiated = [];
    }

    /**
     * Initiate associated model traits. System will look for static method with "__init__" prefix.
     * You can also define __describe__ methods for model analysis (see SchematicEntity).
     *
     * @param bool $analysis Must be set to true while static reflection analysis.
     */
    final protected static function initialize(bool $analysis = false)
    {
        $state = $class = static::class;

        if ($analysis) {
            //Normal and initialization for analysis must load different methods
            $state = "{$class}@static";

            $prefix = '__describe__';
        } else {
            $prefix = '__init__';
        }

        if (isset(self::$initiated[$state])) {
            //Already initiated (not for analysis)
            return;
        }

        foreach (get_class_methods($class) as $method) {
            if (strpos($method, $prefix) === 0) {
                forward_static_call(['static', $method]);
            }
        }

        self::$initiated[$state] = true;
    }
}