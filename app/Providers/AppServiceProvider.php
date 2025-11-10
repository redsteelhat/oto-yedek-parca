<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\Notifications\Channels\SmsChannel;
use App\Services\TenantService;
use App\Models\Setting;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register SMS notification channel
        Notification::extend('sms', function ($app) {
            return new SmsChannel($app->make(\App\Services\SmsService::class));
        });

        $tenantService = app(TenantService::class);

        View::composer('*', function ($view) use ($tenantService) {
            static $sharedTenant = null;
            static $sharedBranding = null;

            if ($sharedBranding === null) {
                $tenant = $tenantService->getCurrentTenant();
                $tenantId = $tenant?->id;

                $defaultPrimary = '#2563EB';
                $defaultSecondary = '#1E40AF';

                $logoPath = $tenant->logo ?? Setting::getValue('site_logo', null, $tenantId);
                $faviconPath = $tenant->favicon ?? Setting::getValue('site_favicon', null, $tenantId);

                $branding = [
                    'name' => $tenant->name ?? config('app.name', 'Yedek Parça'),
                    'primary_color' => $tenant->primary_color ?? Setting::getValue('primary_color', $defaultPrimary, $tenantId) ?? $defaultPrimary,
                    'secondary_color' => $tenant->secondary_color ?? Setting::getValue('secondary_color', $defaultSecondary, $tenantId) ?? $defaultSecondary,
                    'logo' => $logoPath,
                    'favicon' => $faviconPath,
                ];

                foreach (['logo', 'favicon'] as $assetKey) {
                    $path = $branding[$assetKey];
                    if ($path && !Str::startsWith($path, ['http://', 'https://'])) {
                        $branding[$assetKey . '_url'] = Storage::disk('public')->exists($path)
                            ? Storage::url($path)
                            : null;
                    } else {
                        $branding[$assetKey . '_url'] = $path;
                    }
                }

                $sharedTenant = $tenant;
                $sharedBranding = $branding;
            }

            $view->with('currentTenant', $sharedTenant);
            $view->with('tenantBranding', $sharedBranding);
        });
    }
}
