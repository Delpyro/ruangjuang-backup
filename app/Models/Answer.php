<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'id_question',
        'answer',
        'image',
        'is_correct',
        'points',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points' => 'integer',
    ];

    /**
     * Get the question that owns the answer.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'id_question');
    }

    /**
     * Scope a query to only include correct answers.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope a query to order answers by points (descending).
     */
    public function scopeOrderByPoints($query)
    {
        return $query->orderBy('points', 'desc');
    }

    /**
     * Check if the answer has an image.
     */
    public function hasImage(): bool
    {
        return !empty($this->image);
    }

    /**
     * Get the answer with truncated text for display.
     */
    public function getShortAnswerAttribute(): string
    {
        return strlen($this->answer) > 50 
            ? substr(strip_tags($this->answer), 0, 50) . '...' 
            : strip_tags($this->answer);
    }

    /**
     * Get the points with sign for display.
     */
    public function getPointsWithSignAttribute(): string
    {
        if ($this->points > 0) {
            return '+' . $this->points;
        } elseif ($this->points < 0) {
            return $this->points;
        } else {
            return '0';
        }
    }
}