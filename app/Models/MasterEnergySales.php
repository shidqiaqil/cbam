<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterEnergySales extends Model
{
    use HasFactory;

    protected $table = 'master_energy_sales';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'en_name',
        'use_product',
        'en_mgt_name',
        'uom',
        'quantity',
    ];
}
