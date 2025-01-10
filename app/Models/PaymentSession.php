<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSession extends Model
{
    protected $fillable = ['wallet_id', 'status', 'amount'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
