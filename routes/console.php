<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Verificar noticias nuevas cada 5 minutos y enviar push notifications
Schedule::command('push:noticias')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/push.log'));

// Evaluaciones de desempeño: no necesitan aviso inmediato, alcanza cada hora.
// Antes de activarlo por primera vez correr `php artisan push:evaluaciones
// --solo-marcar` para no notificar todo el historial de golpe.
Schedule::command('push:evaluaciones')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/push.log'));
