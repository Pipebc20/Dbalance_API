<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    public $incrementing = false; // Desactiva el auto-incremento

    protected $fillable = ['categoria', 'monto', 'fecha', 'descripcion', 'user_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Gasto::max('id') + 1;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
