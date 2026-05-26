<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'summary', 'cover_url', 'release_year', 'is_high_performance'
    ];

    // Relación Muchos a Muchos con Géneros
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    // Un juego tiene muchas calificaciones/interacciones
    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }
}
