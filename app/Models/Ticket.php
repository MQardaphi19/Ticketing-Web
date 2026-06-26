<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'user_id',
        'category_id',
        'assigned_to',
        'status',
        'priority',
        'sla_due_date',
        'resolved_at',
    ];

    protected $casts = [
        'sla_due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'status' => 'string',
        'priority' => 'string',
    ];

    protected $appends = ['sla_status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function getSlaStatusAttribute(): string
    {
        if ($this->resolved_at) {
            return 'met';
        }

        if (now()->gt($this->sla_due_date)) {
            return 'overdue';
        }

        return 'on-track';
    }

    public function isOverdue(): bool
    {
        return $this->sla_due_date
            && now()->gt($this->sla_due_date)
            && in_array($this->status, ['open', 'in_progress']);
    }
}
