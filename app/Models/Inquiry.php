<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
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
}
