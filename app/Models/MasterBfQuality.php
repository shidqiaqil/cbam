<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MasterBfQuality extends Model
{
    //
    use HasFactory;

    protected $table = 'master_bf_qualities';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'classification',
        'sub_class',
        'sub_subclass',
        'quantity',

    ];
}
