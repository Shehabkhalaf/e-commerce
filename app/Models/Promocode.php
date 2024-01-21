<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    use HasFactory;
    protected $fillable = [
        'promocode',
        'started_at',
        'expired_at',
        'discount'
    ];
    public $timestamps = false;
    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime'
    ];
}
