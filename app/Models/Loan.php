<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    protected $fillable = [
        'customer_id',
        'car_id',
        'user_id',
        'price',
        'downpayment',
        'value_trade',
        'long_term',
        'interest_rate',
        'taxes_rate',
        'minimun_payment',
        'loan_date',
        'start_payment',
        'late_fee',
        'days_late',
        'pago_automatico',
        'pay_documentation',
        'pay_placa',
        'total_financed',
        'balance'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment_loan()
    {
        return $this->hasMany(PaymentLoan::class);
    }

    public function schedule_payment()
    {
        return $this->hasMany(SchedulePayment::class);
    }

}
