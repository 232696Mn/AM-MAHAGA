<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_data', function (Blueprint $table) {
            $table->id();
            
            // Kolom dari Spreadsheet (menggunakan snake_case)
            $table->string('material_name')->nullable(); // Dari: Material Name
            $table->integer('po_psn')->nullable();       // Dari: PO PSN
            $table->integer('po_other')->nullable();     // Dari: PO OTHER
            $table->integer('stock_mahaga')->nullable(); // Dari: Stock Mahaga
            $table->integer('total_material')->nullable(); // Dari: Total Material
            $table->integer('target_produksi')->nullable(); // Dari: Target Produksi
            $table->integer('sisa_material')->nullable(); // Dari: Sisa Material
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_data');
    }
};