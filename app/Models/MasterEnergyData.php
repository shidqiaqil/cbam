<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterEnergyData extends Model
{
    use HasFactory;

    protected $table = 'master_energy_data';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'plant_code',
        'plant_name',
        'criteria',
        'energy_name',
        'unit',
        'quantity',
    ];
}
