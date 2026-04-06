<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterBfPciCoal extends Model
{
    use HasFactory;

    protected $table = 'master_bf_pci_coal';

    protected $fillable = [
        'plant',
        'period_month',
        'period_year',
        'item',
        'brand',
        'sub_brand',
        'quantity',
    ];
}
