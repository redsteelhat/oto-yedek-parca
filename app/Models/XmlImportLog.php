<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XmlImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'status',
        'total_items',
        'imported_items',
        'updated_items',
        'failed_items',
        'error_message',
        'log_details',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'log_details' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
