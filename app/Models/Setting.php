<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'tenant_id',
    ];

    /**
     * Get setting value by key (tenant-aware)
     */
    public static function getValue($key, $default = null, $tenantId = null)
    {
        $tenantService = app(\App\Services\TenantService::class);

        if ($tenantId === null) {
            $tenantId = $tenantService->getCurrentTenantId();
        }

        $query = static::where('key', $key);

        if ($tenantId !== null) {
            $setting = (clone $query)->where('tenant_id', $tenantId)->first();

            if ($setting) {
                return static::castValue($setting->value, $setting->type);
            }
        }

        // Fallback to global (tenant_id is null)
        $globalSetting = static::where('key', $key)
            ->whereNull('tenant_id')
            ->first();

        return $globalSetting ? static::castValue($globalSetting->value, $globalSetting->type) : $default;
    }

    /**
     * Set setting value by key (tenant-aware)
     */
    public static function set($key, $value, $type = 'text', $tenantId = null)
    {
        $tenantService = app(\App\Services\TenantService::class);

        if ($tenantId === null) {
            $tenantId = $tenantService->getCurrentTenantId();
        }

        $setting = static::firstOrNew([
            'key' => $key,
            'tenant_id' => $tenantId,
        ]);

        if (!$setting->exists) {
            $template = static::where('key', $key)
                ->whereNull('tenant_id')
                ->first();

            $setting->group = $template->group ?? 'general';
            $setting->label = $template->label ?? $key;
            $setting->description = $template->description ?? null;
        }

        $setting->value = $value;
        $setting->type = $type;

        $setting->save();

        return $setting;
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, $type)
    {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
            case 'integer':
                return is_numeric($value) ? (int) $value : null;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Get all settings by group (tenant-aware)
     */
    public static function getByGroup($group, $tenantId = null)
    {
        $tenantService = app(\App\Services\TenantService::class);

        if ($tenantId === null) {
            $tenantId = $tenantService->getCurrentTenantId();
        }

        $query = static::where('group', $group);

        if ($tenantId !== null) {
            $tenantSettings = (clone $query)->where('tenant_id', $tenantId)->get();

            if ($tenantSettings->isNotEmpty()) {
                return $tenantSettings;
            }
        }

        return static::where('group', $group)
            ->whereNull('tenant_id')
            ->get();
    }
}
