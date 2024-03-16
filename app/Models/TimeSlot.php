<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;
    protected $guarded = [];
protected $casts = [
        'status' => 'integer',
    ];
    protected $table = 'time_slots';
}
