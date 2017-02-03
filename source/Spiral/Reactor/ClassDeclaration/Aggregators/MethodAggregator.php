<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Reactor\ClassDeclaration\Aggregators;

use Spiral\Reactor\ClassDeclaration\MethodDeclaration;
use Spiral\Reactor\DeclarationAggregator;
use Spiral\Reactor\DeclarationInterface;

/**
 * Method aggregation. Can automatically create constant on demand.
 *
 * @method MethodDeclaration add(MethodDeclaration $element)
 */
class MethodAggregator extends DeclarationAggregator
{
    /**
     * @param array $constants
     */
    public function __construct(array $constants)
    {
        parent::__construct([MethodDeclaration::class], $constants);
    }

    /**
     * Get named element by it's name.
     *
     * @param string $name
     *
     * @return MethodDeclaration|DeclarationInterface
     */
    public function get(string $name): MethodDeclaration
    {
        if (!$this->has($name)) {
            //Automatically creating constant
            $method = new MethodDeclaration($name);
            $this->add($method);

            return $method;
        }

        return parent::get($name);
    }
}