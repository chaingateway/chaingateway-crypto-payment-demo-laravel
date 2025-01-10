<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['address', 'private_key'];

    public function paymentSessions()
    {
        return $this->hasMany(PaymentSession::class);
    }
}
