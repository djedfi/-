<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trim_id',
        'style_id',
        'branch_id',
        'vin',
        'stock_number',
        'year',
        'precio',
        'doors',
        'color',
        'mileage',
        'transmission',
        'condition_car',
        'fuel_type',
        'estado',
        'fuel_economy',
        'engine',
        'drivetrain',
        'wheel_size',
        'url_info'
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

    public function trim()
    {
        return $this->belongsTo(Trim::class);
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
