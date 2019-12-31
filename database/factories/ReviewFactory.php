<?php
use App\Product;
use Faker\Generator as Faker;
/* @var Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Review::class, function (Faker $faker) {
    return [
    	'product_id' => function(){
    		return Product::all()->random();	
    	},
        'customer' => $faker->name,
        'review' => $faker->paragraph,
        'star' => $faker->numberBetween(0,5)
    ];
});