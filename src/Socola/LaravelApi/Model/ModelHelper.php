<?php
/**
 * Created by PhpStorm.
 * User: Socola
 * Date: 27/10/2018
 * Time: 10:30 AM
 */

namespace Socola\LaravelApi\Model;


use Illuminate\Database\Eloquent\Builder;

trait ModelHelper
{
    public static function scopeFindByName(Builder $query, $name)
    {
        return $query->where('name', $name)->first();
    }

    public static function scopeFindBySlug(Builder $query, $slug)
    {
        return $query->where('slug', $slug)->first();
    }
}
