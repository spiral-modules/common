<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Tests\Reactor;

use Interop\Container\ContainerInterface;
use Spiral\Reactor\ClassDeclaration;
use Spiral\Reactor\FileDeclaration;
use Spiral\Reactor\NamespaceDeclaration;

class DeclarationsTest extends \PHPUnit_Framework_TestCase
{
    //Simple test which touches a lot of methods
    public function testClassDeclaration()
    {
        $declaration = new ClassDeclaration('MyClass');
        $declaration->setExtends('Record');
        $this->assertSame('Record', $declaration->getExtends());

        $declaration->addInterface('Traversable');
        $this->assertSame(['Traversable'], $declaration->getInterfaces());

        $this->assertTrue($declaration->hasInterface('Traversable'));
        $declaration->removeInterface('Traversable');
        $this->assertSame([], $declaration->getInterfaces());

        $declaration->constant('BOOT')
            ->setValue(true)
            ->setComment('Always boot');

        $this->assertTrue($declaration->getConstants()->has('BOOT'));
        $this->assertSame(true, $declaration->getConstants()->get('BOOT')->getValue());

        $declaration->property('names')
            ->setAccess(ClassDeclaration\PropertyDeclaration::ACCESS_PRIVATE)
            ->setComment(['This is names', '', '@var array'])
            ->setDefault(['Anton', 'John']);

        $this->assertTrue($declaration->getProperties()->has('names'));
        $this->assertSame(['Anton', 'John'],
            $declaration->getProperties()->get('names')->getDefault());

        $method = $declaration->method('sample');
        $method->parameter('input')->setType('int');
        $method->parameter('output')->setType('int')->setDefault(null)->setPBR(true);
        $method->setAccess(ClassDeclaration\MethodDeclaration::ACCESS_PUBLIC)->setStatic(true);

        $method->setSource([
            '$output = $input;',
            'return true;'
        ]);

        $this->assertSame(
            preg_replace('/\s+/', '', 'class MyClass extends Record
            {
                /**
                 * Always boot
                 */
                const BOOT = true
            
                /**
                 * This is names
                 *
                 * @var array
                 */
                private $names = [
                    \'Anton\',
                    \'John\'
                ];
            
                public function sample(int $input, int &$output = null)
                {
                    $output = $input;
                    return true;
                }
            }'),
            preg_replace('/\s+/', '', $declaration->render())
        );

        return $declaration;
    }

    public function testFileDeclaration()
    {
        $declaration = new FileDeclaration('Spiral\\Custom_Namespace', 'This is test file');
        $declaration->addUse(ContainerInterface::class, 'Container');


        $this->assertSame('Spiral\\Custom_Namespace', $declaration->getNamespace());

        $declaration->addElement($this->testClassDeclaration());

        $this->assertSame(
            preg_replace('/\s+/', '', '
            <?php
            /**
             * This is test file 
             */
             namespace Spiral\\Custom_Namespace;
             
             use Interop\Container\ContainerInterface as Container;
             
             class MyClass extends Record
             {
                 /**
                  * Always boot
                  */
                 const BOOT = true
            
                 /**
                  * This is names
                  *
                  * @var array
                  */
                 private $names = [
                     \'Anton\',
                     \'John\'
                 ];
            
                 public function sample(int $input, int &$output = null)
                 {
                     $output = $input;
                     return true;
                 }
             }'),
            preg_replace('/\s+/', '', (string)$declaration)
        );
    }

    public function testNamespaceDeclaration()
    {
        $declaration = new NamespaceDeclaration('Spiral\\Custom_Namespace');
        $declaration->addUse(ContainerInterface::class, 'Container');

        $declaration->addElement($this->testClassDeclaration());

        $this->assertSame(
            preg_replace('/\s+/', '', '
             namespace Spiral\\Custom_Namespace { 
                 use Interop\Container\ContainerInterface as Container;
                 
                 class MyClass extends Record
                 {
                     /**
                      * Always boot
                      */
                     const BOOT = true
                
                     /**
                      * This is names
                      *
                      * @var array
                      */
                     private $names = [
                         \'Anton\',
                         \'John\'
                     ];
                
                     public function sample(int $input, int &$output = null)
                     {
                         $output = $input;
                         return true;
                     }
                 }
             }'),
            preg_replace('/\s+/', '', $declaration->render())
        );
    }
}