<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Inquiry extends Model
{
    const 旁观者 = 0;
    const 提问者 = 1;
    const 回答者 = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'content', 'secret', 'user_id', 'department_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Inquiry $inquiry) {
            foreach ($inquiry->inquiryReply as $inquiryReply) {
                $inquiryReply->delete();
            }
        });
    }

    public function getRepliedAttribute()
    {
        $lastestReply = $this->inquiryReplies()->orderBy('created_at', 'desc')->first();
        return $lastestReply && $this->user->id !== $lastestReply->user->id;
    }

    /**
     * 此问题的回复
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inquiryReplies()
    {
        return $this->hasMany('App\Models\InquiryReply');
    }

    /**
     * 此问题的提问者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 此问题涉及的部门
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function getAuthUserAttribute()
    {
        $authUser = Auth::user();
        if ($authUser->id === $this->user->id) {
            return static::提问者;
        }

        if ($authUser->hasPermission('view_all_inquiry')
            || ($authUser->hasPermission('view_owned_inquiry') && $authUser->department->id === $this->department->id)
        ) {
            return static::回答者;
        }

        return static::旁观者;
    }

    public function get提问者Attribute()
    {
        return $this->auth_user == static::提问者;
    }

    public function get回答者Attribute()
    {
        return $this->auth_user == static::回答者;
    }

    public function get旁观者Attribute()
    {
        return $this->auth_user == static::旁观者;
    }

    public function hasSecret()
    {
        return strlen($this->secret) > 0;
    }

    public function getDisplaySecretAttribute()
    {
        return $this->CanDiplaySecret() ? $this->secret : "******";
    }

    public function CanDiplaySecret()
    {
        return $this->提问者 || $this->回答者;
    }
}
