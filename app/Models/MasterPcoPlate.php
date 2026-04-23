<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterPcoPlate extends Model
{
    use HasFactory;

    protected $table = 'master_pco_plates';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'class',
        'quantity',
    ];
}
