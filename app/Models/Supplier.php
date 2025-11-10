<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'xml_url',
        'xml_username',
        'xml_password',
        'xml_type',
        'update_frequency',
        'last_import_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_import_at' => 'datetime',
    ];

    protected $hidden = [
        'xml_password',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function xmlMappings()
    {
        return $this->hasMany(SupplierXmlMapping::class)->orderBy('sort_order');
    }

    public function importLogs()
    {
        return $this->hasMany(XmlImportLog::class)->latest();
    }

    public function latestImportLog()
    {
        return $this->hasOne(XmlImportLog::class)->latestOfMany();
    }
}
