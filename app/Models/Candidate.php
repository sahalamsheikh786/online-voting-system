<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    public const PARTIES = [
        'Independent',
        'Unity Party',
        'Student Union',
        'Citizen ',
        'Forward Nepal',
    ];

    protected $fillable = [
        'district_id',
        'name',
        'party',
        'age',
        'position',
        'image_path',
        'vision_path',
        'email',
        'is_active',
    ];

    protected $casts = [
        'age' => 'integer',
        'is_active' => 'boolean',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
