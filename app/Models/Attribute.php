<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttributeType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribute extends Model
{
    use HasFactory;
    protected $guarded = [];
protected $casts = [
        'status' => 'integer',
    ];

    // public function attributeTypes(): BelongsTo
    // {
    //     return $this->belongsTo(AttributeType::class, 'PIVOT','attribute_type_id', 'id')->withTimestamps();
    // }
}
