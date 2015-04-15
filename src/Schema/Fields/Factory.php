<?php

namespace Kalnoy\Cruddy\Schema\Fields;

use Illuminate\Database\Eloquent\Relations\Relation;
use Kalnoy\Cruddy\Schema\BaseFactory;
use Kalnoy\Cruddy\Entity;

/**
 * Field Factory class.
 *
 * @since 1.0.0
 */
class Factory extends BaseFactory {

    /**
     * @var array
     */
    protected $macros = [
        'increments' => 'Kalnoy\Cruddy\Schema\Fields\Types\Primary',
        'string' => 'Kalnoy\Cruddy\Schema\Fields\Types\String',
        'text' => 'Kalnoy\Cruddy\Schema\Fields\Types\Text',
        'email' => 'Kalnoy\Cruddy\Schema\Fields\Types\Email',
        'password' => 'Kalnoy\Cruddy\Schema\Fields\Types\Password',
        'datetime' => 'Kalnoy\Cruddy\Schema\Fields\Types\DateTime',
        'time' => 'Kalnoy\Cruddy\Schema\Fields\Types\Time',
        'date' => 'Kalnoy\Cruddy\Schema\Fields\Types\Date',
        'boolean' => 'Kalnoy\Cruddy\Schema\Fields\Types\Boolean',
        'bool' => 'Kalnoy\Cruddy\Schema\Fields\Types\Boolean',
        'file' => 'Kalnoy\Cruddy\Schema\Fields\Types\File',
        'image' => 'Kalnoy\Cruddy\Schema\Fields\Types\Image',
        'integer' => 'Kalnoy\Cruddy\Schema\Fields\Types\Integer',
        'float' => 'Kalnoy\Cruddy\Schema\Fields\Types\Float',
        'compute' => 'Kalnoy\Cruddy\Schema\Fields\Types\Computed',
        'computed' => 'Kalnoy\Cruddy\Schema\Fields\Types\Computed',
        'enum' => 'Kalnoy\Cruddy\Schema\Fields\Types\Enum',
        'slug' => 'Kalnoy\Cruddy\Schema\Fields\Types\Slug',
    ];

    /**
     * Generate timestamp columns.
     *
     * They are disabled by default.
     *
     * @param Entity $entity
     * @param Collection            $collection
     * @param bool                  $hide
     * @param bool                  $disable
     *
     * @return void
     */
    public function timestamps($entity, $collection, $hide = false, $disable = null)
    {
        $this->resolve('datetime', $collection, [ 'created_at' ])
            ->unique()
            ->hide($hide)
            ->disable($disable ? true : Entity::CREATE);

        $this->resolve('datetime', $collection, [ 'updated_at' ])
            ->unique()
            ->hide($hide)
            ->disable($disable !== false);
    }

    /**
     * Add relation field type.
     *
     * @param Entity $entity
     * @param Collection            $collection
     * @param string                $id
     * @param string                $ref
     * @param bool                  $inline
     *
     * @return BasicRelation
     */
    public function relates($entity, $collection, $id, $ref = null, $inline = false)
    {
        if ($ref === null) $ref = str_plural($id);

        $ref = $entity->getEntitiesRepository()->resolve($ref);

        $model = $entity->getRepository()->newModel();

        if ( ! method_exists($model, $id))
        {
            $className = get_class($model);

            throw new \RuntimeException("The target model [{$className}] doesn't have relation [{$id}] defined.");
        }

        $relation = $model->$id();

        if ( ! $relation instanceof Relation)
        {
            $className = get_class($model);

            throw new \RuntimeException("The method [{$id}] of model [{$className}] did not return valid relation.");
        }

        $relationClassName = class_basename($relation);
        $className = __NAMESPACE__.'\\'.($inline ? 'InlineTypes' : 'Types').'\\'.$relationClassName;

        if ( ! class_exists($className))
        {
            throw new \RuntimeException("Cruddy does not know how to handle [{$relationClassName}] relation.");
        }

        $instance = new $className($entity, $id, $ref, $relation);

        $collection->add($instance);

        return $instance;
    }

    /**
     * Create inline relation field.
     *
     * @param Entity $entity
     * @param Collection            $collection
     * @param string                $id
     * @param string                $ref
     *
     * @return InlineRelation
     */
    public function embed($entity, $collection, $id, $ref = null)
    {
        return $this->relates($entity, $collection, $id, $ref, true);
    }

}