<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'address',
        'governorate',
        'city',
        'postal',
        'phone',
        'status',
        'promocode',
        'total_price'
    ];
    public function orderDetails(): hasMany
    {
        return $this->hasMany(OrderDetails::class);
    }
}
