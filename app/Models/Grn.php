<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Grn extends Model
{
    protected $fillable = [
        'lpo_no', 'supplier_name', 'lpo_date', 'supplier_code', 'requested_by',
        'inv_no', 'department', 'inv_date', 'project_name'
    ];

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }
}
