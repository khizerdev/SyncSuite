<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchItem extends Model
{
    use HasFactory;

    protected $fillable = ['batch_id', 'than_supply_item_id'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function thanSupplyItem(): BelongsTo
    {
        return $this->belongsTo(ThanSupplyItem::class);
    }
}