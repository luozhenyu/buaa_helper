<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\User;

class UserObserver
{
    /**
     * 监听用户创建事件.
     *
     * @param  User $user
     * @return void
     */
    public function created(User $user)
    {
        $property = Property::where('name', 'grade')->firstOrFail();
        $user->properties()->attach($property->id);

        $property = Property::where('name', 'class')->firstOrFail();
        $user->properties()->attach($property->id);

        $property = Property::where('name', 'political_status')->firstOrFail();
        $user->properties()->attach($property->id);

        $property = Property::where('name', 'native_place')->firstOrFail();
        $user->properties()->attach($property->id);

        $property = Property::where('name', 'financial_difficulty')->firstOrFail();
        $user->properties()->attach($property->id);
    }

    /**
     * 监听用户删除事件.
     *
     * @param  User $user
     * @return void
     */
    public function deleting(User $user)
    {
        //
    }
}
