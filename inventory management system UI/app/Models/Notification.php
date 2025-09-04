<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'email_sent_to',
        'email_sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime'
    ];

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'low_stock' => 'red',
            'auto_restock' => 'green',
            'delivery' => 'blue',
            'system' => 'gray',
            default => 'blue'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'low_stock' => '⚠️',
            'auto_restock' => '🔄',
            'delivery' => '🚚',
            'system' => '⚙️',
            default => '📢'
        };
    }
}
