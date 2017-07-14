<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryReply extends Model
{
    const 追问 = 0;
    const 回复 = 1;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inquiry_id', 'user_id', 'content', 'secret'
    ];

    protected $touches = ['inquiry'];

    /**
     * 此回复的作者
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 此回复所属问题
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inquiry()
    {
        return $this->belongsTo('App\Models\Inquiry');
    }

    public function getReplyTypeAttribute()
    {
        $author = $this->inquiry->user;
        if ($author->id === $this->user->id) {
            return static::追问;
        }
        return static::回复;
    }

    public function getReplyTypeNameAttribute()
    {
        return $this->追问 ? '追问' : '回复';
    }

    public function hasSecret()
    {
        return strlen($this->secret) > 0;
    }

    public function get追问Attribute()
    {
        return $this->reply_type === static::追问;
    }


    public function get回复Attribute()
    {
        return $this->reply_type === static::回复;
    }

    public function getDisplaySecretAttribute()
    {
        return $this->inquiry->CanDiplaySecret() ? $this->secret : "******";
    }
}
