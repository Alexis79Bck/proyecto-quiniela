<?php

namespace App\Presentation\Console\Commands;

use App\Events\MatchResultAvailable;
use App\Events\MatchStarted;
use App\Events\PredictionReminder;
use App\Events\NewQuinielaAvailable;
use App\Events\WinnersAnnounced;
use App\Events\LeaderboardUpdated;
use App\Models\User;
use Illuminate\Console\Command;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {--type=all : Tipo de notificación a probar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el sistema de notificaciones Pusher';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');

        $this->info('Probando sistema de notificaciones Pusher...');

        $user = User::first();

        if (!$user) {
            $this->error('No hay usuarios en la base de datos. Ejecuta las migraciones y seeders primero.');
            return Command::FAILURE;
        }

        $this->info("Usuario de prueba: {$user->name} (ID: {$user->id})");

        $types = $type === 'all' ? ['quiniela', 'match', 'result', 'leaderboard', 'reminder', 'winners'] : [$type];

        foreach ($types as $testType) {
            $this->testNotification($testType, $user);
        }

        $this->info('✅ Pruebas de notificaciones completadas.');
        $this->info('Revisa los logs y la consola del navegador para verificar la recepción.');

        return Command::SUCCESS;
    }

    /**
     * Test a specific notification type.
     */
    protected function testNotification(string $type, User $user): void
    {
        $this->info("\nProbando notificación: {$type}");

        match ($type) {
            'quiniela' => event(new NewQuinielaAvailable(
                1,
                'Quiniela de Prueba',
                'Descripción de prueba',
                now()->toDateTimeString(),
                now()->addDays(7)->toDateTimeString()
            )),
            'match' => event(new MatchStarted(
                1,
                'Brasil',
                'Argentina',
                now()->toDateTimeString(),
                'Fase de Grupos'
            )),
            'result' => event(new MatchResultAvailable(
                1,
                'Brasil',
                'Argentina',
                2,
                1,
                'Fase de Grupos'
            )),
            'leaderboard' => event(new LeaderboardUpdated(
                1,
                'Quiniela de Prueba',
                [
                    ['user_id' => 1, 'name' => 'Usuario 1', 'score' => 100],
                    ['user_id' => 2, 'name' => 'Usuario 2', 'score' => 90],
                ],
                10
            )),
            'reminder' => event(new PredictionReminder(
                $user->id,
                1,
                'Brasil',
                'Argentina',
                now()->addHours(2)->toDateTimeString(),
                'Quiniela de Prueba'
            )),
            'winners' => event(new WinnersAnnounced(
                1,
                'Quiniela de Prueba',
                [
                    ['user_id' => 1, 'name' => 'Usuario 1', 'position' => 1, 'score' => 100],
                    ['user_id' => 2, 'name' => 'Usuario 2', 'position' => 2, 'score' => 90],
                ],
                'Premio en efectivo'
            )),
            default => $this->warn("Tipo de notificación no válido: {$type}"),
        };

        $this->info("  ✓ Evento {$type} disparado");
    }
}
