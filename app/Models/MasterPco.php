<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterPco extends Model
{
    //
    use HasFactory;

    protected $table = 'master_pcos';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'criteria',
        'unit',
        'quantity',
    ];
}
