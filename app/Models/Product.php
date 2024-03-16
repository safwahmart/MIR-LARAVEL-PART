<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
protected $casts = [
        'status' => 'integer',
    ];

    public function unit() {
        return $this->belongsTo('App\Models\Unit', 'unit_id', 'id');
    }
    public function category() {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }
    public function brand() {
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id');
    }
    public function productImage() {
        return $this->hasMany('App\Models\ProductImages');
    }
}
