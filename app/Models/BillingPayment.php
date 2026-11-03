<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingPayment extends Model
{
    protected $fillable = ['amount', 'currency', 'paid_at', 'reference', 'notes', 'created_by'];
    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'datetime'];
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
