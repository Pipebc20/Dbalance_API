<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'categoria', 'monto_limite'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
