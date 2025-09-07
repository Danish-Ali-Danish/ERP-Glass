<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_requisition_id',
        'item_code',
        'description',
        'uom',
        'quantity',
    ];

    public function requisition()
    {
        return $this->belongsTo(MaterialRequisition::class, 'material_requisition_id');
    }
}
