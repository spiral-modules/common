<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer\Prototypes;

use Psr\Log\LoggerAwareInterface;
use Spiral\Core\Component;
use Spiral\Core\Container\InjectableInterface;
use Spiral\Debug\Traits\LoggerTrait;
use Spiral\Tokenizer\Exceptions\LocatorException;
use Spiral\Tokenizer\Reflections\ReflectionFile;
use Spiral\Tokenizer\Tokenizer;
use Spiral\Tokenizer\TokenizerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Base class for Class and Invocation locators.
 */
class AbstractLocator extends Component implements InjectableInterface, LoggerAwareInterface
{
    use LoggerTrait;

    /**
     * Parent injector/factory.
     */
    const INJECTOR = Tokenizer::class;

    /**
     * @invisible
     *
     * @var TokenizerInterface
     */
    protected $tokenizer = null;

    /**
     * @var Finder
     */
    protected $finder = null;

    /**
     * @param TokenizerInterface $tokenizer Required to provide ReflectionFile.
     * @param Finder             $finder
     */
    public function __construct(TokenizerInterface $tokenizer, Finder $finder)
    {
        $this->tokenizer = $tokenizer;
        $this->finder = $finder;
    }

    /**
     * Available file reflections. Generator.
     *
     * @return ReflectionFile[]|\Generator
     */
    protected function availableReflections(): \Generator
    {
        /**
         * @var SplFileInfo
         */
        foreach ($this->finder->getIterator() as $file) {
            $reflection = $this->tokenizer->fileReflection((string)$file);

            if ($reflection->hasIncludes()) {
                //We are not analyzing files which has includes, it's not safe to require such reflections
                continue;
            }

            /*
             * @var ReflectionFile $reflection
             */
            yield $reflection;
        }
    }

    /**
     * Safely get class reflection, class loading errors will be blocked and reflection will be
     * excluded from analysis.
     *
     * @param string $class
     *
     * @return \ReflectionClass
     */
    protected function classReflection(string $class): \ReflectionClass
    {
        $loader = function ($class) {
            throw new LocatorException("Class '{$class}' can not be loaded");
        };

        //To suspend class dependency exception
        spl_autoload_register($loader);

        try {
            //In some cases reflection can thrown an exception if class invalid or can not be loaded,
            //we are going to handle such exception and convert it soft exception
            return new \ReflectionClass($class);
        } catch (\Throwable $e) {
            $this->getLogger()->error(
                "Unable to resolve class '{class}', error '{message}'",
                ['class' => $class, 'message' => $e->getMessage()]
            );

            throw new LocatorException($e->getMessage(), $e->getCode());
        } finally {
            spl_autoload_unregister($loader);
        }
    }

    /**
     * Get every class trait (including traits used in parents).
     *
     * @param string $class
     *
     * @return array
     */
    protected function fetchTraits(string $class): array
    {
        $traits = [];

        while ($class) {
            $traits = array_merge(class_uses($class), $traits);
            $class = get_parent_class($class);
        }

        //Traits from traits
        foreach (array_flip($traits) as $trait) {
            $traits = array_merge(class_uses($trait), $traits);
        }

        return array_unique($traits);
    }
}
