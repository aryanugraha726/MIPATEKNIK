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
        // Alter work_order_release to remove detail columns
        Schema::table('work_order_release', function (Blueprint $table) {
            $table->dropColumn([
                'material',
                'model',
                'job_qty',
                'power',
                'desc_part',
                'part_qty',
                'id_satuan'
            ]);
        });

        // Create work_order_release_details
        Schema::create('work_order_release_details', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id');
            $table->string('material')->nullable();
            $table->string('model')->nullable();
            $table->integer('job_qty')->nullable();
            $table->string('power')->nullable();
            $table->text('desc_part')->nullable();
            $table->integer('part_qty')->nullable();
            $table->string('id_satuan')->nullable();
            $table->timestamps();

            // Foreign Key
            $table->foreign('job_id')->references('job_id')->on('work_order_release')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_release_details');

        Schema::table('work_order_release', function (Blueprint $table) {
            $table->string('material')->nullable();
            $table->string('model')->nullable();
            $table->integer('job_qty')->nullable();
            $table->string('power')->nullable();
            $table->text('desc_part')->nullable();
            $table->integer('part_qty')->nullable();
            $table->string('id_satuan')->nullable();
        });
    }
};
