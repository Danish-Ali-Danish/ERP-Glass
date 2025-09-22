<?php

// app/Models/WorkOrderItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'outer_w',
        'outer_h',
        'inner_w',
        'inner_h',
        'qty',
        'sqm',
        'lm',
        'chargeable_sqm',
        'amount',
        'instructions',
    ];

    // Relationship: Item belongs to one WorkOrder
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
