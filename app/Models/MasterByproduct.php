<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterByproduct extends Model
{
    use HasFactory;

    protected $table = 'master_byproducts';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'source',
        'group',
        'quantity',
    ];
}
