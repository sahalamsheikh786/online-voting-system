<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ElectionSetting extends Model
{
    protected $fillable = [
        'district_id',
        'election_title',
        'is_active',
        'started_at',
        'paused_at',
        'remaining_seconds',
        'ends_at',
        'ended_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
        'remaining_seconds' => 'integer',
        'ends_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function hasEnded(): bool
    {
        if ($this->ended_at instanceof Carbon) {
            return true;
        }

        if ($this->paused_at instanceof Carbon) {
            return false;
        }

        return $this->is_active
            && $this->ends_at instanceof Carbon
            && now()->greaterThanOrEqualTo($this->ends_at);
    }

    public function isPaused(): bool
    {
        return $this->paused_at instanceof Carbon && $this->remaining_seconds !== null;
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
