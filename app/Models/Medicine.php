<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'brand',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
   
}
