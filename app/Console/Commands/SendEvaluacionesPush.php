<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendEvaluacionesPush extends Command
{
    protected $signature = 'push:evaluaciones {--solo-marcar : Marca como notificadas las evaluaciones actuales sin enviar nada}';
    protected $description = 'Enviar push notifications personalizadas por evaluaciones de desempeño nuevas';

    public function handle()
    {
        // Traer combinaciones ya notificadas desde la BD principal
        $notificadas = DB::table('notified_evaluaciones')
            ->get()
            ->mapWithKeys(fn($n) => [$n->legajo . '_' . $n->fecha => true]);

        // Traer todas las evaluaciones desde INASI viejo
        $todasEvaluaciones = DB::table('munimer_inasi.in_desempeno')->get();

        // Filtrar las no notificadas (columnas UPPERCASE en INASI)
        $evaluaciones = $todasEvaluaciones->filter(
            fn($e) => !isset($notificadas[$e->LEGAJO . '_' . $e->FECHA])
        );

        if ($evaluaciones->isEmpty()) {
            $this->info('No hay evaluaciones nuevas para notificar.');
            return 0;
        }

        // Sembrado inicial: deja el historial marcado como ya notificado para
        // que la primera corrida del cron no dispare una avalancha de pushes.
        if ($this->option('solo-marcar')) {
            foreach ($evaluaciones as $eval) {
                $this->marcarNotificada($eval);
            }

            $this->info("Marcadas como notificadas sin enviar: {$evaluaciones->count()}");
            return 0;
        }

        if (!config('services.webpush.public_key') || !config('services.webpush.private_key')) {
            $this->error('Faltan VAPID_PUBLIC_KEY / VAPID_PRIVATE_KEY en el .env. No se envio nada.');
            return 1;
        }

        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);
        $sent = 0;
        $failed = 0;
        $noSub = 0;

        foreach ($evaluaciones as $eval) {
            $subscriptions = PushSubscription::where('legajo', $eval->LEGAJO)->get();

            if ($subscriptions->isEmpty()) {
                $noSub++;
            } else {
                // Sin puntuacion ni observaciones en el cuerpo: la notificacion
                // se ve en la pantalla bloqueada del celular.
                $payload = json_encode([
                    'title' => 'Nueva Evaluación de Desempeño',
                    'body' => 'Tenés una evaluación nueva disponible. Tocá para verla.',
                    'url' => '/evaluaciones',
                ]);

                foreach ($subscriptions as $sub) {
                    $subscription = Subscription::create([
                        'endpoint' => $sub->endpoint,
                        'publicKey' => $sub->p256dh,
                        'authToken' => $sub->auth,
                    ]);
                    $webPush->queueNotification($subscription, $payload);
                }

                $this->info("Enviando evaluación para legajo {$eval->LEGAJO}");
            }

            $this->marcarNotificada($eval);
        }

        $results = $webPush->flush();

        foreach ($results as $result) {
            if ($result->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                if ($result->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint', $result->getEndpoint())->delete();
                }
            }
        }

        $this->info("Enviadas: {$sent}, Fallidas: {$failed}, Sin suscripción: {$noSub}");

        return 0;
    }

    private function marcarNotificada($eval): void
    {
        DB::table('notified_evaluaciones')->insertOrIgnore([
            'legajo' => $eval->LEGAJO,
            'fecha'  => $eval->FECHA,
            'notified_at' => now(),
        ]);
    }
}
