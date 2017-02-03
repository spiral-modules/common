<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\tests\Cases\Models;

use Spiral\Models\SchematicEntity;

//todo: improve test coverage
class SchematicEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testFillable()
    {
        $schema = [SchematicEntity::SH_SECURED => []];

        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        $entity = new SchematicEntity([], $schema);
        $entity->setFields($data);
        $this->assertSame($data, $entity->getFields());
    }

    public function testSecured()
    {
        $schema = [SchematicEntity::SH_SECURED => '*'];

        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        $entity = new SchematicEntity([], $schema);
        $entity->setFields($data);
        $this->assertSame([], $entity->getFields());
    }

    public function testPartiallySecured()
    {
        $schema = [
            SchematicEntity::SH_SECURED  => '*',
            SchematicEntity::SH_FILLABLE => ['a', 'b'],
        ];

        $data = ['a' => 1, 'b' => 2, 'c' => 3];

        $entity = new SchematicEntity([], $schema);
        $entity->setFields($data);
        $this->assertSame(['a' => 1, 'b' => 2], $entity->getFields());
    }

    public function getSetters()
    {
        $schema = [
            SchematicEntity::SH_MUTATORS => [
                'setter' => ['a' => 'intval'],
            ],
        ];

        $entity = new SchematicEntity([], $schema);
        $entity->setField('a', '123');

        $this->assertInternalType('int', $entity->getField('a'));
        $this->assertSame(123, $entity->getField('a'));

        $entity->a = '800';
        $this->assertInternalType('int', $entity->a);
        $this->assertSame(800, $entity->a);
    }

    public function testGetters()
    {
        $schema = [
            SchematicEntity::SH_MUTATORS => [
                'getter' => ['a' => 'intval'],
            ],
        ];

        $entity = new SchematicEntity([], $schema);

        $entity->setField('a', false);
        $this->assertInternalType('int', $entity->getField('a'));
        $this->assertInternalType('bool', $entity->packValue()['a']);

        $entity->a = 8000.1;
        $this->assertInternalType('int', $entity->a);
        $this->assertInternalType('float', $entity->packValue()['a']);
    }
}
