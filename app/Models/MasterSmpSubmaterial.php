<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterSmpSubmaterial extends Model
{
    use HasFactory;

    protected $table = 'master_smp_submaterial';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'classification',
        'sub_class',
        'unit',
        'quantity',
    ];
}
