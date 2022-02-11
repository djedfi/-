<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulePayment extends Model
{
    use HasFactory;

    protected $table = 'schedule_payments';

    protected $fillable = [
        'loan_id',
        'user_id',
        'date_programable',
        'date_end'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(SchedulePayment::class);
    }
}
