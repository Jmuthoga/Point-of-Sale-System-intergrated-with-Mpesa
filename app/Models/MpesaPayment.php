<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaPayment extends Model
{
    use HasFactory;

    // Specify which fields are mass assignable
    protected $fillable = [
        'transaction_type',
        'trans_id',
        'trans_time',
        'business_short_code',
        'bill_ref_number',
        'invoice_number',
        'trans_amount',
        'msisdn',
        'first_name',
        'middle_name',
        'last_name',
        'order_id',
    ];

    /**
     * Relationship to Order
     * You must have an `Order` model and the `order_id` field must match.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
