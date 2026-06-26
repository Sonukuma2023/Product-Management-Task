<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'category_id',
    'name',
    'price',
    'quantity',
    'description',
    'sku_code',
    'image'
];

// Relationship to get the user who created this product
public function creator()
{
    return $this->belongsTo(User::class, 'user_id');
}
}