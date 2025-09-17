<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    protected $fillable = [
        'grn_no', 'lpo_id', 'supplier_name', 'supplier_code',
        'requested_by', 'department_id', 'inv_no', 'inv_date', 'project_name',
    ];
    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }
    public function lpo()
    {
        return $this->belongsTo(Lpo::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

}
