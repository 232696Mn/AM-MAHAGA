<?php
// app/Models/MonitoringData.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringData extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_name', 
        'po_psn', 
        'po_other', 
        'stock_mahaga', 
        'total_material', 
        'target_produksi', 
        'sisa_material', 
        // ... kolom lain yang Anda butuhkan
    ];

    protected $table = 'monitoring_data'; 
}