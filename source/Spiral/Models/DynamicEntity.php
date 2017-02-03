<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Models;

use Spiral\Models\Exceptions\EntityException;
use Spiral\Models\Prototypes\AbstractEntity;

/**
 * This is analog of DataEntity based on mutable arrays to define mutator and accessors.
 *
 * @see DataEntity
 */
class DynamicEntity extends AbstractEntity
{
    /**
     * List of fields must be hidden from publicFields() method.
     *
     * @see publicValue()
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Set of fields allowed to be filled using setFields() method.
     *
     * @see setFields()
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * List of fields not allowed to be filled by setFields() method. Replace with and empty array
     * to allow all fields.
     *
     * By default all entity fields are settable! Opposite behaviour has to be described in entity
     * child implementations.
     *
     * @see setFields()
     *
     * @var array|string
     */
    protected $secured = '*';

    /**
     * @see setField()
     *
     * @var array
     */
    protected $setters = [];

    /**
     * @see getField()
     *
     * @var array
     */
    protected $getters = [];

    /**
     * Accessor used to mock field data and filter every request thought itself.
     *
     * @see getField()
     * @see setField()
     *
     * @var array
     */
    protected $accessors = [];

    /**
     * {@inheritdoc}
     */
    public function isPublic(string $field): bool
    {
        return !in_array($field, $this->hidden);
    }

    /**
     * Check if field can be set using setFields() method.
     *
     * @see   setField()
     * @see   $fillable
     * @see   $secured
     *
     * @param string $field
     *
     * @return bool
     */
    protected function isFillable(string $field): bool
    {
        if (!empty($this->fillable)) {
            return in_array($field, $this->fillable);
        }

        if ($this->secured === '*') {
            return false;
        }

        return !in_array($field, $this->secured);
    }

    /**
     * Check and return name of mutator (getter, setter, accessor) associated with specific field.
     *
     * @param string $field
     * @param string $mutator Mutator type (setter, getter, accessor).
     *
     * @return mixed|null
     *
     * @throws EntityException
     */
    protected function getMutator(string $field, string $mutator)
    {
        //We do support 3 mutators: getter, setter and accessor, all of them can be
        //referenced to valid field name by adding "s" at the end
        $mutator = $mutator . 's';

        if (isset($this->{$mutator}[$field])) {
            return $this->{$mutator}[$field];
        }

        return null;
    }
}