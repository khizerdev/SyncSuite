<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_date',
        'product_group_id',
        'job_type',
        'department_id',
        'party_id'
    ];

    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}