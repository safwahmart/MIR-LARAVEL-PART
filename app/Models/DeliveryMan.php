<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMan extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'status' => 'integer',
        'delivery_type_id' => 'integer',
    ];
}
