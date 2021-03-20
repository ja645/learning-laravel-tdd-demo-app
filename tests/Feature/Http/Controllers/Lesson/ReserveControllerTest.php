<?php

namespace Tests\Feature\Http\Controllers\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use Facade\FlareClient\Http\Response;
use App\Notifications\ReservationCompleted;
use App\Notifications\OverShipped;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Factories\Traits\CreatesUser;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    use CreatesUser;

    public function testInvoke_正常系()
    {
        Notification::fake();
        $lesson = \App\Models\Lesson::factory()->create();
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->post("/lessons/{$lesson->id}/reserve");

        $response->assertStatus(302);
        $response->assertRedirect("/lessons/{$lesson->id}");

        $this->assertDatabaseHas('reservations', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);

        Notification::assertSentTo(
            $user,
            ReservationCompleted::class,
            function (ReservationCompleted $notification) use ($lesson) {
                return $notification->lesson->id === $lesson->id;
            }
        );
    }

    public function testInvoke_異常系()
    {
        Notification::fake();
        $lesson = \App\Models\Lesson::factory()->create(['capacity' => 1]);
        $anotherUser = $this->createUser();
        $lesson->reservations()->save(\App\Models\Reservation::factory()->make(['user_id' => $anotherUser->id]));
        
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->from("/lessons/{$lesson->id}")->post("/lessons/{$lesson->id}/reserve");

        $response->assertStatus(302);
        $response->assertRedirect("/lessons/{$lesson->id}");
        $response->assertSessionHasErrors();
        //メッセージの中身まで確認したい場合は以下の2行も追加
        $error = session('errors')->first();
        $this->assertStringContainsString('予約できません。', $error);

        $this->assertDatabaseMissing('reservations', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);

        Notification::assertNotSentTo($user, ReservationCompleted::class);
    }
}
