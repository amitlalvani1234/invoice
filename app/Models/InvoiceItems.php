<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItems extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id','item_description','price','qty','total'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'invoice_id','id');
    }

}
