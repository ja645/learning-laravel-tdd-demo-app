<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Lesson;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\Factories\Traits\CreatesUser;
use Tests\TestCase;
//factoryメソッドを使うためのトレイト
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUser;

    /**
     * @param int $capacity
     * @param int $reservationCount
     * @param string $expectedVacancyLevelMark
     * @param string $button
     * @dataProvider dataShow
     */ 
    public function testShow(int $capacity, int $reservationCount, string $expectedVacancyLevelMark, string $button)
    {
        $lesson = \App\Models\Lesson::factory()->create(['name' => '楽しいヨガレッスン', 'capacity' => $capacity]);
        for ($i = 0; $i < $reservationCount; $i++){
            $user = \App\Models\User::factory()->create();
            \App\Models\UserProfile::factory()->create(['user_id' => $user->id]);
            \App\Models\Reservation::factory()->create(['lesson_id' => $lesson->id, 'user_id' => $user->id]);
        }

        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->get("/lessons/{$lesson->id}");

        $response->assertStatus(Response::HTTP_OK);
        
        $response->assertSee($lesson->name);
        $response->assertSee("空き状況： {$expectedVacancyLevelMark}");

        $response->assertSee($button, false);
    }

    public function dataShow()
    {
        $button = '<button class="btn btn-primary">このレッスンを予約する</button>';
        $span = '<span class="btn btn-primary disabled">予約できません</span>';

        return[
            '空き十分' => [
                'capacity' => 6,
                'reservationCount' => 1,
                'expectedVacancyLevelMark' => '◎',
                'button' => $button,
            ],
            '空きわずか' => [
                'capacity' => 6,
                'reservationCount' => 2,
                'expectedVacancyLevelMark' => '△',
                'button' => $button,
            ],
            '空きなし' => [
                'capacity' => 1,
                'reservationCount' => 1,
                'expectedVacancyLevelMark' => '×',
                'button' => $span,
            ],
        ];
    }
}
