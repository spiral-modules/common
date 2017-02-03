<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Reactor;

use Spiral\Reactor\ClassDeclaration;
use Spiral\Reactor\FileDeclaration;
use Spiral\Reactor\NamespaceDeclaration;

class ReplaceTest extends \PHPUnit_Framework_TestCase
{
    public function testReplace()
    {
        $declaration = new ClassDeclaration('MyClass');
        $declaration->setExtends('Record');

        $declaration->property('names')
            ->setAccess(ClassDeclaration\PropertyDeclaration::ACCESS_PRIVATE)
            ->setComment(['This is foxes', '', '@var array'])
            ->setDefault(['name' => 11, 'value' => 'hi', 'test' => []]);

        $method = $declaration->method('sample');
        $method->parameter('input')->setType('int');
        $method->parameter('output')->setType('int')->setDefault(null)->setPBR(true);
        $method->setAccess(ClassDeclaration\MethodDeclaration::ACCESS_PUBLIC)->setStatic(true);
        $method->setComment('Get some foxes');

        $method->setSource([
            '$output = $input;',
            'return true;'
        ]);

        $declaration->addTrait('Spiral\Debug\Traits\LoggerTrait');
        $this->assertTrue($declaration->hasTrait('Spiral\Debug\Traits\LoggerTrait'));

        $namespace = new NamespaceDeclaration('Namespace');
        $namespace->setComment('All about foxes');

        $namespace->addElement($declaration);

        $file = new FileDeclaration();
        $file->getComment()->addLine('Full file of foxes');
        $file->addElement($namespace);

        $this->assertSame(
            preg_replace('/\s+/', '', '<?php
            /**
             * Full file of foxes
             */
            namespace Namespace {
                class MyClass extends Record
                {
                    use Spiral\Debug\Traits\LoggerTrait;
            
                    /**
                     * This is foxes
                     *
                     * @var array
                     */
                    private $names = [
                        \'name\'  => 11,
                        \'value\' => \'hi\',
                        \'test\'  => []
                    ];
            
                    /**
                     * Get some foxes
                     */
                    public function sample(int $input, int &$output = null)
                    {
                        $output = $input;
                        return true;
                    }
                }
            }'),
            preg_replace('/\s+/', '', $file->render())
        );

        $file->replace('foxes', 'dogs');

        $this->assertSame(
            preg_replace('/\s+/', '', '<?php
                /**
                 * Full file of dogs
                 */
                namespace Namespace {
                    class MyClass extends Record
                    {
                        use Spiral\Debug\Traits\LoggerTrait;
                
                        /**
                         * This is dogs
                         *
                         * @var array
                         */
                        private $names = [
                            \'name\'  => 11,
                            \'value\' => \'hi\',
                            \'test\'  => []
                        ];
                
                        /**
                         * Get some dogs
                         */
                        public function sample(int $input, int &$output = null)
                        {
                            $output = $input;
                            return true;
                        }
                    }
                }'),
            preg_replace('/\s+/', '', $file->render()
            )
        );


    }
}