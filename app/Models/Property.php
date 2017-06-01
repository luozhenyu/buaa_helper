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
}
