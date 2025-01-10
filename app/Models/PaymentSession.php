<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSession extends Model
{
    protected $fillable = ['wallet_id', 'status', 'amount', 'currency', 'received_amount', 'webhook_id'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
