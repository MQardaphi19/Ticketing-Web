<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_query',
        'predicted_category_id',
        'confidence_score',
        'is_correct',
    ];

    protected $casts = [
        'confidence_score' => 'integer',
        'is_correct' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function predictedCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'predicted_category_id');
    }
}
