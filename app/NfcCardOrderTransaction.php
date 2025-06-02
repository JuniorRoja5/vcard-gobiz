<?php
 
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfcCardOrderTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'nfc_card_order_transaction_id', 
        'nfc_card_order_id',
        'payment_transaction_id',
        'payment_method',
        'currency',
        'amount',
        'invoice_number',
        'invoice_prefix',
        'invoice_details',
        'payment_status',
        'status',
    ];

    public function nfcCardOrder()
    {
        return $this->belongsTo(NfcCardOrder::class, 'nfc_card_order_id', 'nfc_card_order_id');
    }
}
