<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Attribute;

class AttributeType extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'status' => 'integer',
    ];

    // public function attributes(): HasMany
    // {
    //     return $this->hasMany(Attribute::class);
    // }
}
