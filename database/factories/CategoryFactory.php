<?php

namespace Database\Factories;

use Faker\Factory;
use App\Entities\Category;

class CategoryFactory
{
    public static function make(array $override = []): Category
    {
        $faker = Factory::create();

        return new Category(
            null,
            $override['name'] ?? $faker->word(),
            $override['description'] ?? $faker->sentence()
        );
    }
}