<?php

namespace Database\Factories;

use Faker\Factory;
use App\Entities\Post;

class PostFactory
{
    public static function make(array $override = []): Post
    {
        $faker = Factory::create();
        $imageUrl = 'https://placehold.co/600x400/'.$faker->rgbColor().'/'.$faker->rgbColor().'?font=montserrat&text='.$faker->word();
        return new Post(
            null,
            $override['title'] ?? $faker->sentence(6),
            $override['description'] ?? $faker->paragraph(),
            $override['content'] ?? $faker->paragraph(15),
            $override['image'] ?? $imageUrl,
            $override['views'] ?? rand(0, 1000),
            (new \DateTime())->format('Y-m-d H:i:s'),
        );
    }
}