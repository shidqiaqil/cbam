<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_pco_coils', function (Blueprint $table) {
            $table->id();
            $table->string('plant');
            $table->string('period_month');
            $table->integer('period_year');
            $table->string('class');
            $table->decimal('quantity', 20, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_pco_coils');
    }
};
