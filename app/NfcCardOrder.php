<?php

namespace App;

use App\User;
use App\BusinessCard;
use App\NfcCardDesign;
use App\NfcCardOrderTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NfcCardOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'nfc_card_order_id', 
        'user_id',
        'nfc_card_id',
        'card_id',
        'nfc_card_transaction_id',
        'delivery_address',
        'total_price',
        'delivery_note',
        'order_status',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function nfcCardDesign()
    {
        return $this->belongsTo(NfcCardDesign::class, 'nfc_card_id', 'nfc_card_id');
    }

    public function nfcCardTransaction()
    {
        return $this->belongsTo(NfcCardOrderTransaction::class, 'nfc_card_transaction_id', 'nfc_card_transaction_id');
    }

    public function businessCard()
    {
        return $this->belongsTo(BusinessCard::class, 'card_id', 'card_id');
    }
}
