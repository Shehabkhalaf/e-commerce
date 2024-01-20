<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'category_id',
        'description',
        'price',
        'discount',
        'stock',
        'barcode'
    ];
    public function category(): belongsTo
    {
        return  $this->belongsTo(Category::class);
    }
    public function product_images(): hasMany
    {
        return $this->hasMany(Product_Images::class);
    }
    public function product_colors(): hasMany
    {
        return  $this->hasMany(Product_Colors::class);
    }
}
