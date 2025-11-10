<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends BaseAdminController
{
    public function index()
    {
        $groups = [
            'general' => 'Genel Ayarlar',
            'payment' => 'Ödeme Ayarları',
            'shipping' => 'Kargo Ayarları',
            'email' => 'E-posta Ayarları',
            'seo' => 'SEO Ayarları',
            'sms' => 'SMS Ayarları',
        ];

        // Get current tenant ID
        $tenantId = $this->getCurrentTenantId();
        
        $settings = Setting::where('tenant_id', $tenantId)
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');
        
        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        // Get current tenant ID
        $tenantId = $this->getCurrentTenantId();
        
        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)
                ->where('tenant_id', $tenantId)
                ->first();
            
            if ($setting) {
                // Cast value based on type
                if ($setting->type === 'boolean') {
                    $value = isset($value) ? true : false;
                } elseif ($setting->type === 'json') {
                    $value = json_encode($value);
                }

                $setting->update(['value' => $value]);
            } else {
                // Create new setting if it doesn't exist
                Setting::create([
                    'key' => $key,
                    'value' => $value,
                    'type' => 'text',
                    'group' => $this->getGroupForKey($key),
                    'label' => $key,
                    'tenant_id' => $tenantId,
                ]);
            }
        }

        // Handle file uploads with security validation
        if ($request->hasFile('logo')) {
            try {
                $logoPath = \App\Services\FileUploadService::uploadImage(
                    $request->file('logo'),
                    'settings',
                    2048 // 2MB max for logo
                );
                $this->updateSetting('site_logo', $logoPath);
            } catch (\Exception $e) {
                return back()->with('error', 'Logo yüklenirken hata: ' . $e->getMessage());
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                $faviconPath = \App\Services\FileUploadService::uploadImage(
                    $request->file('favicon'),
                    'settings',
                    512 // 512KB max for favicon
                );
                $this->updateSetting('site_favicon', $faviconPath);
            } catch (\Exception $e) {
                return back()->with('error', 'Favicon yüklenirken hata: ' . $e->getMessage());
            }
        }

        // Update email config if email settings changed
        $this->updateEmailConfig();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Ayarlar başarıyla güncellendi.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable',
            'type' => 'required|in:text,textarea,number,boolean,json',
            'group' => 'required|string',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Add tenant_id to validated data
        $validated['tenant_id'] = $this->getCurrentTenantId();
        
        Setting::create($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Ayar başarıyla eklendi.');
    }

    /**
     * Get group for a setting key
     */
    private function getGroupForKey($key)
    {
        if (str_starts_with($key, 'payment_')) return 'payment';
        if (str_starts_with($key, 'shipping_')) return 'shipping';
        if (str_starts_with($key, 'email_')) return 'email';
        if (str_starts_with($key, 'seo_')) return 'seo';
        if (str_starts_with($key, 'sms_')) return 'sms';
        return 'general';
    }

    /**
     * Update or create a setting
     */
    private function updateSetting($key, $value, $type = 'string')
    {
        // Get current tenant ID
        $tenantId = $this->getCurrentTenantId();
        
        Setting::updateOrCreate(
            [
                'key' => $key,
                'tenant_id' => $tenantId,
            ],
            [
                'value' => $value,
                'type' => $type,
                'group' => $this->getGroupForKey($key),
                'label' => $key,
                'tenant_id' => $tenantId,
            ]
        );
    }

    /**
     * Update email configuration from settings
     */
    private function updateEmailConfig()
    {
        // Get current tenant ID
        $tenantId = $this->getCurrentTenantId();
        
        $emailSettings = Setting::where('group', 'email')
            ->where('tenant_id', $tenantId)
            ->get()
            ->pluck('value', 'key');
        
        // Update .env file or config cache
        // Note: In production, you might want to use a config cache approach
        // For now, we'll just update the settings table
        // The mail config can read from settings table via a helper
    }
}
