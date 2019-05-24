<?php

use Faker\Generator as Faker;
use App\Models\Pocket;

$factory->define(Pocket::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'category_id' => rand(1, 4),
        'expenditure' => rand(1, 99),
        'expenditure_date' => now(),
        'is_necessary' => rand(0, 1),
        'comment' => "",
    ];
});
