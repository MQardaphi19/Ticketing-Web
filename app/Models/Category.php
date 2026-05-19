<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sla_hours',
    ];

    protected $casts = [
        'sla_hours' => 'integer',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function knowledgeBase(): HasMany
    {
        return $this->hasMany(KnowledgeBase::class);
    }

    public function chatbotLogs(): HasMany
    {
        return $this->hasMany(ChatbotLog::class);
    }
}
