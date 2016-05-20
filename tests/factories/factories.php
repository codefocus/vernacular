<?php
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory('ImaginaryWebsite', function ($faker) {
    return [
        'title'         => $faker->title,
        'description'   => $faker->realText(mt_rand(32,  128), 3),
        'content'       => $faker->realText(mt_rand(256, 1024), 3),
        //'foo'           => (mt_rand() > 0.5) ? null : str_random(10),
        'foo'           => str_random(10),
    ];
});
