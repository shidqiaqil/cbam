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
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_employee', 7)->after('name');
            $table->string('id_org_unit', 10);
            $table->string('id_job_position', 10);
            $table->string('id_department', 10);
            $table->string('id_division', 10);
            $table->string('team', 100);
            $table->string('department', 100);
            $table->string('division', 100);
            $table->string('role', 30);
            $table->string('sso_hash');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
