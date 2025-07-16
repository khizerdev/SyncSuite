<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricMeasurement extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function thanIssueItems()
{
    return $this->belongsToMany(ThanIssueItem::class, 'design_than_issue_item',
        'fabric_measurement_id',
        'than_issue_item_id'
    );
}
}