<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'req_no', 'date', 'req_date', 'requested_by',
        'department_id', 'project_name', 'remarks', 'delivery_date'
    ];

    public function items(){
        return $this->hasMany(RequisitionItem::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }
}
