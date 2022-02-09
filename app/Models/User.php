<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'first_name',
        'last_name',
        'email',
        'cargo',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function password_reset()
    {
        return $this->hasMany(PasswordReset::class);
    }

    public function user_options()
    {
        return $this->belongsToMany(OptionApp::class,'user_optios','user_id','option_id');
    }

    public function loan()
    {
        return $this->hasMany(Loan::class);
    }

    public function payment_loan()
    {
        return $this->hasMany(PaymentLoan::class);
    }

    public function shedule_payment()
    {
        return $this->hasMany(SchedulePayment::class);
    }
}
