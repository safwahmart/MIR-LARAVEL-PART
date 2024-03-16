<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDiscount extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'status' => 'integer',
        'cod_inside_dhaka' => 'integer',
        'cod_outside_dhaka' => 'integer',
        'free_delivery' => 'integer',
    ];
}
