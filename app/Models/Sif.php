<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sif extends Model
{
    use HasFactory;

    protected $fillable = [
        'sif_no',
        'date',
        'issued_date',
        'requested_by',
        'department_id',
        'project_name',
        'remarks',
    ];

    public function items()
    {
        return $this->hasMany(SifItem::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
