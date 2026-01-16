<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class SendNoticiasPush extends Command
{
    protected $signature = 'push:noticias';
    protected $description = 'Enviar push notification cuando hay noticias nuevas';

    public function handle()
    {
        // Buscar noticias que no hayan sido notificadas aún
        $noticias = DB::table('in_noticia')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('notified_noticias')
                    ->whereColumn('notified_noticias.noticia_id', 'in_noticia.ID');
            })
            ->where('FECHAVTO', '>=', now())
            ->get();

        if ($noticias->isEmpty()) {
            $this->info('No hay noticias nuevas.');
            return 0;
        }

        $subscriptions = PushSubscription::all();

        if ($subscriptions->isEmpty()) {
            $this->info('No hay suscriptores.');
            // Igual marcar como notificadas para no volver a intentar
            foreach ($noticias as $noticia) {
                DB::table('notified_noticias')->insert(['noticia_id' => $noticia->ID]);
            }
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

        foreach ($noticias as $noticia) {
            $payload = json_encode([
                'title' => 'Nueva Noticia',
                'body' => $noticia->TITULO,
                'url' => '/noticia/' . $noticia->ID,
            ]);

            foreach ($subscriptions as $sub) {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->p256dh,
                    'authToken' => $sub->auth,
                ]);

                $webPush->queueNotification($subscription, $payload);
            }

            $this->info("Enviando notificación: {$noticia->TITULO}");

            // Marcar como notificada
            DB::table('notified_noticias')->insert(['noticia_id' => $noticia->ID]);
        }

        // Enviar todas las notificaciones
        $results = $webPush->flush();

        $sent = 0;
        $failed = 0;

        foreach ($results as $result) {
            if ($result->isSuccess()) {
                $sent++;
            } else {
                $failed++;
                // Eliminar suscripciones inválidas
                if ($result->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint', $result->getEndpoint())->delete();
                }
            }
        }

        $this->info("Enviadas: {$sent}, Fallidas: {$failed}");

        return 0;
    }
}
