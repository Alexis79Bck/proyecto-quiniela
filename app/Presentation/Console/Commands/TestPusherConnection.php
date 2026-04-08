<?php

namespace App\Presentation\Console\Commands;

use Illuminate\Console\Command;
use Pusher\Pusher;

class TestPusherConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pusher:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar la conexión a Pusher';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Verificando conexión a Pusher...');

        try {
            $pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );

            // Intentar obtener información del canal
            $channels = $pusher->get_channels();

            $this->info('✅ Conexión a Pusher exitosa!');
            $this->info('Canales disponibles: ' . count($channels->channels ?? []));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error al conectar a Pusher: ' . $e->getMessage());
            $this->warn('Verifica que las credenciales de Pusher estén configuradas correctamente en .env');

            return Command::FAILURE;
        }
    }
}
