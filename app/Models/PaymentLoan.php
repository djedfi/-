<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLoan extends Model
{
    use HasFactory;

    protected $table = 'payments_loan';

    protected $fillable = [
        'loan_id',
        'user_id',
        'description',
        'concepto',
        'monto',
        'date_doit',
        'forma_pago',
        'balance',
        'estado',
        'reason_delete'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
