<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'department',
        'phone',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super-admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    public function isKepalaDiskominfo(): bool
    {
        return $this->role === 'kepala-diskominfo';
    }

    public function isPegawaiDinas(): bool
    {
        return $this->role === 'pegawai-dinas';
    }

    public function isStaff(): bool
    {
        return $this->role === 'teknisi';
    }

    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('role', 'teknisi');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function ticketMessages(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function chatbotLogs(): HasMany
    {
        return $this->hasMany(ChatbotLog::class);
    }
}
