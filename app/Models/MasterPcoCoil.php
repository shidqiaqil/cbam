<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterPcoCoil extends Model
{
    use HasFactory;

    protected $table = 'master_pco_coils';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'class',
        'quantity',
    ];
}
