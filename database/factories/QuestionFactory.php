<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Answer;
use App\Models\Question;
use Faker\Generator as Faker;

$factory->define(Question::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
    ];
});
$factory->state(Question::class, Question::MULTI_CHOICE, function (Faker $faker) {
    return [
        'type' => Question::MULTI_CHOICE,
    ];
});

$factory->afterCreating(Question::class, function (Question $question) {
    return factory(Answer::class)->create([
        'question_id' => $question->id,
    ]);
});
