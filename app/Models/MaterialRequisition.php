<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'req_no',
        'req_date',
        'department_id',
        'project_name',
        'remarks',
        'requested_by',
        'delivery_date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function items()
    {
        return $this->hasMany(MaterialRequisitionItem::class);
    }
}
