<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename project_id to job_id in existing tables (using raw statements for safety against complex constraints in some DBs)
        if (Schema::hasColumn('project', 'project_id')) {
            DB::statement('ALTER TABLE `project` CHANGE `project_id` `job_id` INT NOT NULL');
        }
        if (Schema::hasColumn('subproject', 'project_id')) {
            DB::statement('ALTER TABLE `subproject` CHANGE `project_id` `job_id` INT NOT NULL');
        }
        if (Schema::hasColumn('barang_keluar', 'project_id')) {
            DB::statement('ALTER TABLE `barang_keluar` CHANGE `project_id` `job_id` INT NOT NULL');
        }

        // 2. Modify Vendor
        Schema::table('vendor', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor', 'alamat_vendor')) {
                $table->text('alamat_vendor')->nullable();
                $table->string('phone_vendor')->nullable();
                $table->string('cp_vendor')->nullable();
            }
        });

        // 3. Create shipping_address
        Schema::create('shipping_address', function (Blueprint $table) {
            $table->string('id_lokasi')->primary();
            $table->string('nama_lokasi');
            $table->text('alamat_mipa');
            $table->string('phone_mipa')->nullable();
            $table->string('cp_mipa')->nullable();
            $table->string('email_mipa')->nullable();
        });

        // 4. Create work_order_release
        Schema::create('work_order_release', function (Blueprint $table) {
            $table->integer('job_id')->primary();
            $table->string('customer')->nullable();
            $table->text('jobdesc')->nullable();
            $table->string('po_proyek')->nullable();
            $table->date('tgl_order')->nullable();
            $table->date('jadwal_kirim')->nullable();
            $table->text('alamat_kirim')->nullable();
            $table->string('cp_customer')->nullable();
            $table->string('sales')->nullable();
            $table->string('mail_address')->nullable();
            $table->decimal('estimate_man_hour', 10, 2)->nullable();
            $table->string('material')->nullable();
            $table->string('model')->nullable();
            $table->integer('job_qty')->nullable();
            $table->string('power')->nullable();
            $table->string('priority')->nullable();
            $table->text('desc_part')->nullable();
            $table->integer('part_qty')->nullable();
            $table->string('id_satuan')->nullable();
        });

        // 5. Create material_request
        Schema::create('material_request', function (Blueprint $table) {
            $table->id('no_nota');
            $table->integer('job_id');
            $table->string('id_divisi');
            $table->string('id_karyawan');
            $table->date('tanggal');
            $table->string('status')->default('PENDING_MANAGER');
        });

        // 6. Create material_request_detail
        Schema::create('material_request_detail', function (Blueprint $table) {
            $table->id('id_req_detail');
            $table->unsignedBigInteger('no_nota');
            $table->string('id_barang');
            $table->integer('req_qty');
            
            $table->foreign('no_nota')->references('no_nota')->on('material_request')->onDelete('cascade');
        });

        // 7. Create po
        Schema::create('po', function (Blueprint $table) {
            $table->string('no_po')->primary();
            $table->date('tgl_po');
            $table->string('id_vendor');
            $table->string('id_lokasi');
            $table->integer('job_id');
            $table->string('currency')->default('IDR');
            $table->decimal('ppn', 15, 2)->nullable();
            $table->decimal('pph', 15, 2)->nullable();
            $table->decimal('grand_total', 15, 2)->nullable();
        });

        // 8. Create po_detail
        Schema::create('po_detail', function (Blueprint $table) {
            $table->id('id_po_detail');
            $table->string('no_po');
            $table->string('id_barang');
            $table->integer('qty_po');
            $table->string('id_satuan');

            $table->foreign('no_po')->references('no_po')->on('po')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_detail');
        Schema::dropIfExists('po');
        Schema::dropIfExists('material_request_detail');
        Schema::dropIfExists('material_request');
        Schema::dropIfExists('work_order_release');
        Schema::dropIfExists('shipping_address');

        Schema::table('vendor', function (Blueprint $table) {
            $table->dropColumn(['alamat_vendor', 'phone_vendor', 'cp_vendor']);
        });

        if (Schema::hasColumn('project', 'job_id')) {
            DB::statement('ALTER TABLE `project` CHANGE `job_id` `project_id` INT NOT NULL');
        }
        if (Schema::hasColumn('subproject', 'job_id')) {
            DB::statement('ALTER TABLE `subproject` CHANGE `job_id` `project_id` INT NOT NULL');
        }
        if (Schema::hasColumn('barang_keluar', 'job_id')) {
            DB::statement('ALTER TABLE `barang_keluar` CHANGE `job_id` `project_id` INT NOT NULL');
        }
    }
};
