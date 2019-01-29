<?php

namespace R64\NovaFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // relation ship events
        \Event::listen('update.has-one.relation', function (Model $model, $relation, $attributes, $data) {
            //
            $deleted = array_get($data, 'deleted', false);
            $model::saved(function ($savedModel) use ($relation, $attributes, $data, $deleted) {
                if ($deleted) {
                    $savedModel->{$relation}()->delete();
                } else {
                    $savedModel->{$relation}()->updateOrCreate(array_only($data, 'id'), array_only($data, $attributes));
                }
            });
        });
    }
}
