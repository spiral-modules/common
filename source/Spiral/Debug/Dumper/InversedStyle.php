<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Dumper;

/**
 * Alternative to default debug style.
 */
class InversedStyle extends Style
{
    /**
     * Container element used to inject dump into, usually pre elemnt with some styling.
     *
     * @var string
     */
    protected $container = '<pre style="background-color: #232323; font-family: Monospace;">{dump}</pre>';

    /**
     * Set of styles associated with different dumping properties.
     *
     * @var array
     */
    protected $styles = [
        'common'   => 'color: #E6E1DC',
        'name'     => 'color: #E6E1DC',
        'dynamic'  => 'color: #7d95c1;',
        'maxLevel' => 'color: #ff9900',
        'syntax'   => [
            'common' => 'color: gray',
            '['      => 'color: #E6E1DC',
            ']'      => 'color: #E6E1DC',
            '('      => 'color: #E6E1DC',
            ')'      => 'color: #E6E1DC',
        ],
        'value'    => [
            'string'  => 'color: #A5C261',
            'integer' => 'color: #A5C261',
            'double'  => 'color: #A5C261',
            'boolean' => 'color: #C26230; font-weight: bold;',
        ],
        'type'     => [
            'common'   => 'color: #E6E1DC',
            'object'   => 'color: #E6E1DC',
            'array'    => 'color: #E6E1DC',
            'null'     => 'color: #C26230; font-weight: bold',
            'resource' => 'color: #C26230; font-weight: bold',
        ],
        'access'   => [
            'common'    => 'color: #666',
            'public'    => 'color: #8dc17d',
            'private'   => 'color: #c18c7d',
            'protected' => 'color: #7d95c1',
        ],
    ];
}
