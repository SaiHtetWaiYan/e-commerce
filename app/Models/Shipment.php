<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_agent_id',
        'tracking_number',
        'carrier_name',
        'status',
        'estimated_delivery_date',
        'shipped_at',
        'delivered_at',
        'current_latitude',
        'current_longitude',
        'delivery_proof_image',
        'notes',
        'estimated_delivery_time_from',
        'estimated_delivery_time_to',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'estimated_delivery_date' => 'date',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'current_latitude' => 'decimal:8',
            'current_longitude' => 'decimal:8',
            'estimated_delivery_time_from' => 'string',
            'estimated_delivery_time_to' => 'string',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(ShipmentTrackingEvent::class)->orderByDesc('event_at');
    }

    public function scopeForAgent(Builder $query, int $agentId): Builder
    {
        return $query->where('delivery_agent_id', $agentId);
    }
}
