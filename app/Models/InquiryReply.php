<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryReply extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inquiry_id', 'user_id', 'content',
    ];

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
}
