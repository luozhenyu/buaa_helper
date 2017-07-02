<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PropertyUser extends Pivot
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_value_id',
    ];

    /**
     * 此property拥有的value
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function propertyValue()
    {
        return $this->belongsTo('App\Models\PropertyValue');
    }
}
