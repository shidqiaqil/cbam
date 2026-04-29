<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BEmInstData extends Model
{
    protected $table = 'b_em_inst_data';

    protected $fillable = [
        'period_type',
        'year',
        'period_value',
        'row_order',
        'method',
        'source_stream_name',
        'ad_unit',
        'ncv_value',
        'ncv_unit',
        'ef_value',
        'ef_unit',
        'carbon_content',
        'c_content_unit',
    ];

    protected $casts = [
        'ncv_value'      => 'float',
        'ef_value'       => 'float',
        'carbon_content' => 'float',
        'year'           => 'integer',
        'row_order'      => 'integer',
    ];
}
