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
        Schema::table('work_order_release', function (Blueprint $table) {
            $table->dropColumn('work_scope');
            $table->string('material')->nullable();
            $table->string('model')->nullable();
            $table->string('power')->nullable();
            $table->integer('equip_qty')->nullable();
        });

        Schema::table('work_order_release_details', function (Blueprint $table) {
            $table->dropColumn(['material', 'model', 'job_qty', 'power']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_release', function (Blueprint $table) {
            $table->text('work_scope')->nullable();
            $table->dropColumn(['material', 'model', 'power', 'equip_qty']);
        });

        Schema::table('work_order_release_details', function (Blueprint $table) {
            $table->string('material')->nullable();
            $table->string('model')->nullable();
            $table->integer('job_qty')->nullable();
            $table->string('power')->nullable();
        });
    }
};
