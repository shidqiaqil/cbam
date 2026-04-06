<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_bf_qualities', function (Blueprint $table) {
            $table->id();
            $table->string('plant');
            $table->string('period_month');
            $table->integer('period_year');
            $table->string('classification');
            $table->string('sub_class');
            $table->string('sub_subclass');
            $table->decimal('quantity', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_bf_qualities');
    }
};
