<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendEvaluacionesPush extends Command
{
    protected $signature = 'push:evaluaciones';
    protected $description = 'Enviar push notifications personalizadas por evaluaciones de desempeño nuevas';

    public function handle()
    {
        // Traer combinaciones ya notificadas desde la BD principal
        $notificadas = DB::table('notified_evaluaciones')
            ->get()
            ->mapWithKeys(fn($n) => [$n->legajo . '_' . $n->fecha => true]);

        // Traer todas las evaluaciones desde INASI
        $todasEvaluaciones = DB::connection('mysql1')
            ->table('in_desempeno')
            ->get();

        // Filtrar las no notificadas (columnas UPPERCASE en INASI)
        $evaluaciones = $todasEvaluaciones->filter(
            fn($e) => !isset($notificadas[$e->LEGAJO . '_' . $e->FECHA])
        );

        if ($evaluaciones->isEmpty()) {
            $this->info('No hay evaluaciones nuevas para notificar.');
            return 0;
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
                $observa = $eval->OBSERVA ? ' — ' . $eval->OBSERVA : '';
                $payload = json_encode([
                    'title' => 'Nueva Evaluación de Desempeño',
                    'body' => 'Tu puntuación: ' . $eval->PUNTUACION . $observa,
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

            DB::table('notified_evaluaciones')->insertOrIgnore([
                'legajo' => $eval->LEGAJO,
                'fecha'  => $eval->FECHA,
                'notified_at' => now(),
            ]);
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
}
