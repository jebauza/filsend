<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'lastname' => $faker->lastName,
        'document' => strtoupper($faker->unique()->lexify('?'.$faker->numerify('#######').'?')),
        'phone' => $faker->e164PhoneNumber,
        'email' => $faker->unique()->safeEmail
    ];
});
