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
        'LEGAJO',
        'NOMBRE',
        'DNI',
        'CATEGORIA',
        'CLAVEWEB',
        'FECHABAJ'
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

    // Relacion con Familia
    public function familia()
    {
        return $this->hasMany(Familia::class, 'LEGAJO', 'LEGAJO');
    }

    // Relacion solo con hijos
    public function hijos()
    {
        return $this->hasMany(Familia::class, 'LEGAJO', 'LEGAJO')->where('TIPOFAMI', '>', 1);
    }

    // Obtener cantidad de hijos
    public function getCantidadHijosAttribute()
    {
        return Familia::contarHijos($this->LEGAJO);
    }

    // Verificar si tiene hijos
    public function getTieneHijosAttribute()
    {
        return Familia::tieneHijos($this->LEGAJO);
    }

    // Verificar si está inactivo (fechabaj no nula)
    public function getEstaInactivoAttribute()
    {
        return !empty($this->FECHABAJ) 
           && $this->FECHABAJ !== '0000-00-00' 
           && $this->FECHABAJ !== '0000-00-00 00:00:00';
    }

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

    public function getNameAttribute()
    {
        // Ajusta según el campo real que tengas para el nombre
        return $this->NOMBRE ?? $this->DNI;
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
