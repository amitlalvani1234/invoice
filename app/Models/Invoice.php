<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_no','invoice_date','customer_name','customer_mo','subtotal','gst','gst_inculsive','grand_total','gst_amount'];
    public function invoicesitems()
    {
        return $this->hasMany(InvoiceItems::class);
    }
    public function setCustomerNameAttribute($value)
    {
        $this->attributes['customer_name'] = ucwords(strtolower($value));
    }

    public static function getNextInvoiceNumber()
    {
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $lastInvoiceNo = $lastInvoice ? $lastInvoice->invoice_no : null;
        $prefix = 'INV';

        $lastNumber = preg_replace('/[^0-9]/', '', $lastInvoiceNo);

        $nextNumber = $lastNumber ? intval($lastNumber) + 1 : 1;
        $nextInvoiceNo = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return $nextInvoiceNo;
    }
}
