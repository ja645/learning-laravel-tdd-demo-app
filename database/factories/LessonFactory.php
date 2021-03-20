<?php

namespace Database\Factories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class LessonFactory extends Factory
{
    /**
     * The name of the factory's cprresponding model.
     * 
     * @var string
     */
    protected $model = \App\Models\Lesson::class;

    /**
     * Define the model's default state.
     * 
     * @return array
     */
    public function definition(): array
    {
        //明日から10日後までの間でランダムな日時を生成する
        $startAt = $this->faker->dateTimeBetween('+1 days', '+10 days');
        $startAt->setTime(10, 0, 0);
        $endAt = clone $startAt;
        $endAt->setTime(11, 0, 0);

        return [
            'name' => $this->faker->name,
            'coach_name' => $this->faker->name,
            'capacity' => $this->faker->randomNumber(2),
            'start_at' => $startAt->format('Y-m-d H:i:s'),
            'end_at' => $endAt->format('Y-m-d H:i:s'),
        ];
    }

    public function past()
    {
        $startAt = $this->faker->dateTimeBetween('-10 days', '-1 days');
        $startAt->setTime(10, 0, 0);
        $endAt = clone $startAt;
        $endAt->setTime(11, 0, 0);
        
        return $this->state([
                'start_at' => $startAt->format('Y-m-d H:i:s'),
                'end_at' => $endAt->format('Y-m-d H:i:s'),
            ]);
    }
}


