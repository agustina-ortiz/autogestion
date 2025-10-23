<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $table = 'in_premios';
    
    protected $fillable = [
        'legajo',
        'ano',
        'mes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'legajo', 'legajo');
    }
}