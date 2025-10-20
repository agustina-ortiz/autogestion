<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class InMaestroUserProvider extends EloquentUserProvider
{
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        
        // Si CLAVEWEB está en texto plano (NO RECOMENDADO en producción)
        return $plain === $user->getAuthPassword();
        
        // O si usa otro tipo de hash, por ejemplo MD5:
        // return md5($plain) === $user->getAuthPassword();
        
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // No hacer nada - evitar que intente actualizar el campo password
        return;
    }
}