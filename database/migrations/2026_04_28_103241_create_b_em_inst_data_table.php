<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b_em_inst_data', function (Blueprint $table) {
            $table->id();
            $table->string('period_type')->default('monthly'); // monthly, quarterly, yearly
            $table->integer('year');
            $table->string('period_value'); // e.g., '01'-'12' for monthly, 'Q1'-'Q4' for quarterly, 'annual' for yearly
            $table->integer('row_order');
            $table->string('method');
            $table->string('source_stream_name');
            $table->string('ad_unit')->nullable();
            $table->decimal('ncv_value', 15, 6)->nullable();
            $table->string('ncv_unit')->nullable();
            $table->decimal('ef_value', 15, 6)->nullable();
            $table->string('ef_unit')->nullable();
            $table->decimal('carbon_content', 15, 6)->nullable();
            $table->string('c_content_unit')->nullable();
            $table->timestamps();

            $table->unique(['period_type', 'year', 'period_value', 'row_order'], 'b_em_inst_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b_em_inst_data');
    }
};
