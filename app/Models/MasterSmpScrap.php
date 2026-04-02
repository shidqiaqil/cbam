<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterSmpScrap extends Model
{
    //
    use HasFactory;

    protected $table = 'master_smp_scrap';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'category',
        'sub_category',
        'unit',
        'quantity',
    ];
}
