<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'total_price',
        'payment_method'
    ];
    public function orderDetails(): hasMany
    {
        return $this->hasMany(OrderDetails::class);
    }
    public function orderStatus(): hasOne
    {
        return $this->hasOne(Order_Status::class);
    }
    public function payment(): hasOne
    {
        return $this->hasOne(Payment::class);
    }
}
