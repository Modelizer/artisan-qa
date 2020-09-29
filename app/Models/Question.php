<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $title
 * @property $type
 */
class Question extends Model
{
    /** @var int */
    const SINGLE = 0;

    /** @var int */
    const MULTI_CHOICE = 1;

    /** @var string[] $fillable */
    protected $fillable = [
        'title',
        'type',
        'completed_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * @return bool
     */
    public function markCompleted()
    {
        return $this->update([
            'completed_at' => now()
        ]);
    }
}
