<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'quote_no', 'date', 'to_name', 'company_name', 'project_name',
        'location', 'total', 'vat', 'grand_total', 'terms'
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
