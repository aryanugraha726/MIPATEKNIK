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
        Schema::table('project', function (Blueprint $table) {
            $table->boolean('is_completed')->default(0)->after('management_id');
            $table->date('tanggal_selesai')->nullable()->after('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropColumn(['is_completed', 'tanggal_selesai']);
        });
    }
};
