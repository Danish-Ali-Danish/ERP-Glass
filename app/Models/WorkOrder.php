<?php

// app/Models/WorkOrder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_no',
        'customer_name',
        'customer_mobile',
        'date',
        'work_order_type',
        'customer_ref',
        'processes',
        'extra_price_sqm',
        'extra_total',
    ];

    protected $casts = [
        'processes' => 'array', // JSON <-> array auto convert
        'date' => 'date',
    ];

    // Relationship: One WorkOrder has many items
    public function items()
    {
        return $this->hasMany(WorkOrderItem::class);
    }
}
