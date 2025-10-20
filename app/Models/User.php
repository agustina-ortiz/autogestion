<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'in_maestro';
    protected $primaryKey = 'LEGAJO';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false; // Si la tabla no tiene timestamps

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'DNI',
        'CLAVEWEB',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'CLAVEWEB',
        'remember_token',
    ];

    // Indicar a Laravel qué campo usar para el username
    public function getAuthIdentifierName()
    {
        return 'DNI';
    }

    // Indicar qué campo usar para la contraseña
    public function getAuthPassword()
    {
        return $this->CLAVEWEB;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [

        ];
    }

    public function setPasswordAttribute($value)
    {
        // No hacer nada
    }
}
