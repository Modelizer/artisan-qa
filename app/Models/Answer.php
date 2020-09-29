<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $question_id
 * @property $value
 */
class Answer extends Model
{
    /** @var string[] $fillable */
    protected $fillable = [
        'question_id',
        'value',
    ];
}
