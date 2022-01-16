<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state_id',
        'customer_id',
        'licence',
        'state_licence',
        'first_name',
        'last_name',
        'initial',
        'address_p',
        'address_s',
        'city',
        'zip',
        'telephone_res',
        'telephone_bus',
        'cellphone',
        'email',
        'birthday',
        'ssn'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
