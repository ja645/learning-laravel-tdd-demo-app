<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'plan' => $this->faker->randomElement(['regular', 'gold']),
            'sex' => $this->faker->randomElement(['female', 'male']),
            'age' => $this->faker->numberBetween(0, 100),
        ];
    }
}
