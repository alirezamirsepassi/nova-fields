<?php

namespace R64\NovaFields;

use Illuminate\Support\Str;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class HasOne extends Field
{
    use Configurable;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-fields-has-one';

    /**
     * The base input classes of the field.
     *
     * @var string
     */
    public $inputClasses = '';

    /**
     * The base index classes of the field.
     *
     * @var string
     */
    public $indexClasses = '';

    /**
     * The callback that should be used to resolve the pivot fields.
     *
     * @var callable
     */
    public $fieldsCallback;

    /**
     * Specify the callback to be executed to retrieve the pivot fields.
     *
     * @param  callable $callback
     *
     * @return $this
     */
    public function fields($callback)
    {
        $this->fieldsCallback = $callback;
        return $this;
    }

    /**
     * Show delete button on form.
     *
     * @return \R64\NovaFields\HasOne
     */
    public function showDeleteButton()
    {
        return $this->withMeta(['show-delete-button' => true]);
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed       $resource
     * @param  string|null $attribute
     *
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $attribute = $attribute ?? $this->attribute;

        $value = $resource->{$attribute};

        $value = is_object($value) || is_array($value) ? $value : null;

        $fields = collect(call_user_func($this->fieldsCallback));
        $fields = $fields->whereInstanceOf(Resolvable::class)->map(function ($field) use ($resource, $attribute, $value) {
            /** @var Field $field */
            $key = $field->attribute;
            $cb = $field->resolveCallback;

            $field->withMeta([
                'baseResourceName'    =>  $this->resolveCallback instanceOf \Closure ? $field->resolveCallback : $this->resolveCallback::uriKey(),
                'baseResourceClass'   =>  $this->resolveCallback
            ]);

            if (isset($value->{$key})) {
                $field->value = $cb ? call_user_func($cb, $value->{$key}) :
                    array_get($value->{$key}, object_get($field, 'id', 'id'), $value->{$key});
            }
            return $field;
        });

        $this->resolveUsing(function () use ($value) {
            return $value;
        });

        $this->withMeta(['fields' => $fields]);
        $this->showOnIndex = false;
        $this->showOnCreation = true;
        $this->showOnUpdate = true;
        $this->showOnDetail = true;

        parent::resolve($resource, $attribute);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param  string                                  $requestAttribute
     * @param  object                                  $model
     * @param  string                                  $attribute
     *
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $attributeValue = json_decode($request[$requestAttribute], true);
            $fieldsAttribute = array_pluck(call_user_func($this->fieldsCallback), 'attribute');
            event('update.has-one.relation', [$model, $requestAttribute, $fieldsAttribute, $attributeValue]);
            unset($request[$requestAttribute]);
        }
    }
}
