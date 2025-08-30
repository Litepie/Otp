<?php

namespace Litepie\Otp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Otp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'otps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'identifier',
        'code',
        'type',
        'signature',
        'expires_at',
        'used_at',
        'attempts',
        'max_attempts',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'code',
        'signature',
    ];

    /**
     * Scope a query to only include valid OTPs.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now())
                    ->whereNull('used_at')
                    ->where('attempts', '<', 'max_attempts');
    }

    /**
     * Scope a query to only include expired OTPs.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope a query to only include used OTPs.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUsed(Builder $query): Builder
    {
        return $query->whereNotNull('used_at');
    }

    /**
     * Scope a query for a specific identifier and type.
     *
     * @param Builder $query
     * @param string $identifier
     * @param string $type
     * @return Builder
     */
    public function scopeForIdentifier(Builder $query, string $identifier, string $type = 'default'): Builder
    {
        return $query->where('identifier', $identifier)
                    ->where('type', $type);
    }

    /**
     * Check if the OTP is expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP is used.
     *
     * @return bool
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }

    /**
     * Check if the OTP is valid (not expired, not used, attempts not exceeded).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !$this->isExpired() 
            && !$this->isUsed() 
            && $this->attempts < $this->max_attempts;
    }

    /**
     * Mark the OTP as used.
     *
     * @return bool
     */
    public function markAsUsed(): bool
    {
        $this->used_at = now();
        return $this->save();
    }

    /**
     * Increment the attempts counter.
     *
     * @return bool
     */
    public function incrementAttempts(): bool
    {
        $this->attempts++;
        return $this->save();
    }

    /**
     * Get the time remaining until expiration in seconds.
     *
     * @return int
     */
    public function getTimeRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return $this->expires_at->diffInSeconds(now());
    }

    /**
     * Get the remaining attempts.
     *
     * @return int
     */
    public function getRemainingAttempts(): int
    {
        return max(0, $this->max_attempts - $this->attempts);
    }
}
