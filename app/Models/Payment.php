<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'card_token',
        'status',
        'description',
        'mode',
        'order_id',
        'transaction_id'
    ];
}
