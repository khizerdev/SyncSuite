<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'shift_machine_id',
        'start_time',
        'end_time',
        'run_time',
        'steam_open',
        'steam_closed',
        'temperature',
        'weight',
        'total_dyeing_time',
        'running_time'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'steam_open' => 'datetime',
        'steam_closed' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'lot_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Calculate run time automatically
    public function calculateRunTime(): int
    {
        if ($this->start_time && $this->end_time) {
            $diff = $this->end_time->diffInMinutes($this->start_time);
            $this->run_time = $diff;
            return $diff;
        }
        return 0;
    }
}