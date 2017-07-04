<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'secret',
    ];

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

    /**
     * 此问题的回复
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionReply()
    {
        return $this->hasMany('App\Models\QuestionReply');
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function (Question $question) {
            foreach ($question->questionReply as $questionReply) {
                $questionReply->delete();
            }
        });
    }
}
