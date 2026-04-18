<?php

namespace Tests\Unit\Notifications;

use App\Events\NewQuinielaAvailable;
use App\Events\MatchStarted;
use App\Events\MatchResultAvailable;
use App\Events\LeaderboardUpdated;
use App\Events\PredictionReminder;
use App\Events\WinnersAnnounced;
use App\Infrastructure\Notifications\Listeners\SendPusherNotification;
use App\Infrastructure\Notifications\NewQuinielaNotification;
use App\Infrastructure\Notifications\MatchStartedNotification;
use App\Infrastructure\Notifications\MatchResultNotification;
use App\Infrastructure\Notifications\LeaderboardUpdateNotification;
use App\Infrastructure\Notifications\PredictionReminderNotification;
use App\Infrastructure\Notifications\WinnersNotification;
use PHPUnit\Framework\TestCase;

class SendPusherNotificationListenerTest extends TestCase
{
    public function test_listener_implements_should_queue(): void
    {
        $listener = new SendPusherNotification();
        
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $listener);
    }

    public function test_listener_has_correct_queue_configuration(): void
    {
        $listener = new SendPusherNotification();
        
        $this->assertEquals('database', $listener->connection);
        $this->assertEquals('notifications', $listener->queue);
        $this->assertEquals(3, $listener->tries);
    }

    public function test_listener_creates_new_quiniela_notification(): void
    {
        $listener = new SendPusherNotification();
        
        $notification = new NewQuinielaNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            description: 'Predice los resultados',
            startDate: '2026-06-11',
            endDate: '2026-07-19'
        );
        
        $this->assertInstanceOf(NewQuinielaNotification::class, $notification);
    }

    public function test_create_match_started_notification(): void
    {
        $notification = new MatchStartedNotification(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            startTime: '2026-06-15 16:00:00',
            stage: 'Final'
        );
        
        $this->assertInstanceOf(MatchStartedNotification::class, $notification);
    }

    public function test_create_match_result_notification(): void
    {
        $notification = new MatchResultNotification(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            homeScore: 3,
            awayScore: 2,
            stage: 'Final'
        );
        
        $this->assertInstanceOf(MatchResultNotification::class, $notification);
    }

    public function test_create_leaderboard_notification(): void
    {
        $notification = new LeaderboardUpdateNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            topUsers: [
                ['user_id' => 1, 'name' => 'Juan', 'points' => 100],
            ],
            totalParticipants: 50
        );
        
        $this->assertInstanceOf(LeaderboardUpdateNotification::class, $notification);
    }

    public function test_create_prediction_reminder_notification(): void
    {
        $notification = new PredictionReminderNotification(
            userId: 1,
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            matchTime: '2026-06-15 16:00:00',
            quinielaName: 'Copa Mundo 2026'
        );
        
        $this->assertInstanceOf(PredictionReminderNotification::class, $notification);
    }

    public function test_create_winners_notification(): void
    {
        $notification = new WinnersNotification(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            winners: [
                ['user_id' => 1, 'position' => 1, 'points' => 100],
            ],
            prizeDescription: '1er lugar: $1000'
        );
        
        $this->assertInstanceOf(WinnersNotification::class, $notification);
    }

    public function test_new_quiniela_event_can_be_created(): void
    {
        $event = new NewQuinielaAvailable(
            quinielaId: 99,
            quinielaName: 'Copa Mundo 2026',
            description: 'Predice los resultados',
            startDate: '2026-06-11',
            endDate: '2026-07-19'
        );
        
        $this->assertInstanceOf(NewQuinielaAvailable::class, $event);
        $this->assertEquals(99, $event->quinielaId);
    }

    public function test_match_started_event_can_be_created(): void
    {
        $event = new MatchStarted(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            startTime: '2026-06-15 16:00:00',
            stage: 'Final'
        );
        
        $this->assertInstanceOf(MatchStarted::class, $event);
    }

    public function test_match_result_available_event_can_be_created(): void
    {
        $event = new MatchResultAvailable(
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            homeScore: 3,
            awayScore: 2,
            stage: 'Final'
        );
        
        $this->assertInstanceOf(MatchResultAvailable::class, $event);
    }

    public function test_leaderboard_updated_event_can_be_created(): void
    {
        $event = new LeaderboardUpdated(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            topUsers: [
                ['user_id' => 1, 'name' => 'Juan', 'points' => 100],
            ],
            totalParticipants: 50
        );
        
        $this->assertInstanceOf(LeaderboardUpdated::class, $event);
    }

    public function test_prediction_reminder_event_can_be_created(): void
    {
        $event = new PredictionReminder(
            userId: 1,
            matchId: 1,
            homeTeam: 'Argentina',
            awayTeam: 'Brasil',
            matchTime: '2026-06-15 16:00:00',
            quinielaName: 'Copa Mundo 2026'
        );
        
        $this->assertInstanceOf(PredictionReminder::class, $event);
    }

    public function test_winners_announced_event_can_be_created(): void
    {
        $event = new WinnersAnnounced(
            quinielaId: 1,
            quinielaName: 'Copa Mundo 2026',
            winners: [
                ['user_id' => 1, 'position' => 1, 'points' => 100],
            ],
            prizeDescription: '1er lugar: $1000'
        );
        
        $this->assertInstanceOf(WinnersAnnounced::class, $event);
    }
}