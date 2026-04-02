<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterBf extends Model
{
    //
    use HasFactory;

    protected $table = 'master_bf';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'classification',
        'sub_class',
        'sub_subclass',
        'first_bf',
        'second_bf',
        'total_average',
    ];
}
