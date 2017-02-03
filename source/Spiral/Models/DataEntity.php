<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Models;

use Spiral\Models\Exceptions\EntityException;
use Spiral\Models\Prototypes\AbstractEntity;

/**
 * DataEntity in spiral used to represent basic data set with filters and accessors. Most of spiral
 * models (ORM and ODM, HttpFilters) will extend data entity.
 */
class DataEntity extends AbstractEntity
{
    /**
     * List of fields must be hidden from publicFields() method.
     *
     * @see publicValue()
     *
     * @var array
     */
    const HIDDEN = [];

    /**
     * Set of fields allowed to be filled using setFields() method.
     *
     * @see setFields()
     *
     * @var array
     */
    const FILLABLE = [];

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
    const SECURED = '*';

    /**
     * @see setField()
     *
     * @var array
     */
    const SETTERS = [];

    /**
     * @see getField()
     *
     * @var array
     */
    const GETTERS = [];

    /**
     * Accessor used to mock field data and filter every request thought itself.
     *
     * @see getField()
     * @see setField()
     *
     * @var array
     */
    const ACCESSORS = [];

    /**
     * {@inheritdoc}
     */
    public function isPublic(string $field): bool
    {
        return !in_array($field, static::HIDDEN);
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
        if (!empty(static::FILLABLE)) {
            return in_array($field, static::FILLABLE);
        }

        if (static::SECURED === '*') {
            return false;
        }

        return !in_array($field, static::SECURED);
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
        $target = [];
        switch ($mutator) {
            case self::MUTATOR_ACCESSOR:
                $target = static::ACCESSORS;
                break;
            case self::MUTATOR_GETTER:
                $target = static::GETTERS;
                break;
            case self::MUTATOR_SETTER:
                $target = static::SETTERS;
                break;
        }

        if (isset($target[$field])) {
            return $target[$field];
        }

        return null;
    }
}