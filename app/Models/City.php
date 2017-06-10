<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name',
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\City', 'parent_id', 'id');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('App\Models\City', 'parent_id', 'id');
    }

    /**
     * city tree
     * @return \Illuminate\Support\Collection
     */
    public function tree()
    {
        $tree = [];
        for ($city = $this; $city; $city = $city->parent) {
            $tree[] = $city;
        }
        return array_reverse($tree);
    }

    /**
     * @return string
     */
    public function format()
    {
        $names = [];
        foreach ($this->tree() as $node) {
            $names[] = $node->name;
        }
        return implode(" ", $names);
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function (City $city) {
            foreach ($city->children as $child) {
                $child->delete();
            }
        });
    }
}
