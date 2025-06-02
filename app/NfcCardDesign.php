<?php

namespace App;

use App\NfcCardOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NfcCardDesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'nfc_card_id',
        'nfc_card_name',
        'nfc_card_description',
        'nfc_card_front_image', 
        'nfc_card_back_image',
        'nfc_card_price',
        'status',
    ];

    public function nfcCardOrders()
    {
        return $this->hasMany(NfcCardOrder::class, 'nfc_card_id', 'nfc_card_id');
    }
}
