<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description',
    ];

    /**
     * 此property拥有的value
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function propertyValues()
    {
        return $this->hasMany('App\Models\PropertyValue');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User')
            ->withPivot('property_value_id')
            ->using('App\Models\PropertyUser');
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Property $property) {
            $property->users()->detach();

            foreach ($property->propertyValues as $propertyValue) {
                $propertyValue->delete();
            }
        });
    }
}
