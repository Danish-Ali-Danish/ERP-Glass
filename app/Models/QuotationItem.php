<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id', 'item_id', 'description', 'unit',
        'size', 'color', 'type', 'remarks',

        'quantity', 'unit_price', 'total',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

}
