<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterSinter extends Model
{
    //
    use HasFactory;

    protected $table = 'master_sinters';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'classification',
        'sub_class',
        'quantity',
    ];
}
