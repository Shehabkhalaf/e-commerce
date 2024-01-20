<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static create(array $only)
 */
class Category extends Model
{
    use HasFactory;

    //Make attributes title, status fillable
    protected  $fillable = [
        'title','status'
    ];
    public function products(): hasMany
    {
        return $this->hasMany(Product::class);
    }
}
