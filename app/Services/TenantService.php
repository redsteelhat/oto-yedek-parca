<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class TenantService
{
    /**
     * Get current tenant.
     */
    public function getCurrentTenant(): ?Tenant
    {
        $tenantId = $this->getCurrentTenantId();
        
        if (!$tenantId) {
            return null;
        }

        return Cache::remember("tenant.{$tenantId}", 3600, function () use ($tenantId) {
            return Tenant::find($tenantId);
        });
    }

    /**
     * Get current tenant ID.
     */
    public function getCurrentTenantId(): ?int
    {
        // Session'dan al
        if (Session::has('tenant_id')) {
            return Session::get('tenant_id');
        }

        // Request'ten subdomain'i al
        $subdomain = $this->getSubdomainFromRequest();

        if ($subdomain) {
            $tenant = Tenant::where('subdomain', $subdomain)
                ->orWhere('domain', request()->getHost())
                ->first();

            if ($tenant) {
                Session::put('tenant_id', $tenant->id);
                return $tenant->id;
            }
        }

        // Varsayılan tenant'a düş (isteğe bağlı)
        if (config('tenant.auto_fallback', false)) {
            $defaultTenant = $this->getDefaultTenant();

            if ($defaultTenant) {
                Session::put('tenant_id', $defaultTenant->id);
                return $defaultTenant->id;
            }
        }

        return null;
    }

    /**
     * Set current tenant.
     */
    public function setTenant(Tenant $tenant): void
    {
        Session::put('tenant_id', $tenant->id);
        Cache::forget("tenant.{$tenant->id}");
    }

    /**
     * Clear current tenant.
     */
    public function clearTenant(): void
    {
        Session::forget('tenant_id');
    }

    /**
     * Get subdomain from request.
     */
    public function getSubdomainFromRequest(): ?string
    {
        $host = request()->getHost();
        
        // Local development için
        if (in_array($host, ['localhost', '127.0.0.1', '::1'])) {
            // Query parameter'dan al (development için)
            if ($tenantParam = request()->query('tenant')) {
                return $tenantParam;
            }

            // .env üzerinden belirlenen varsayılan tenant varsa onu dön
            if ($defaultSlug = config('tenant.default_slug')) {
                return $defaultSlug;
            }

            return null;
        }

        // Subdomain'i çıkar (örn: tenant1.site.com -> tenant1)
        $parts = explode('.', $host);
        
        // En az 3 parça olmalı (subdomain.domain.tld)
        if (count($parts) >= 3) {
            // İlk parça subdomain
            $subdomain = $parts[0];
            
            // 'www' veya 'admin' gibi özel subdomain'leri atla
            if (!in_array($subdomain, ['www', 'admin', 'api', 'manage'])) {
                return $subdomain;
            }
        }

        return null;
    }

    /**
     * Check if current user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->isSuperAdmin();
    }

    /**
     * Switch tenant (for super admin).
     */
    public function switchTenant(int $tenantId): void
    {
        if (!$this->isSuperAdmin()) {
            throw new \Exception('Only super admin can switch tenants');
        }

        $tenant = Tenant::findOrFail($tenantId);
        $this->setTenant($tenant);
    }

    /**
     * Get default tenant (fallback).
     */
    public function getDefaultTenant(): ?Tenant
    {
        $defaultSlug = config('tenant.default_slug');

        if ($defaultSlug) {
            $tenant = Tenant::where('slug', $defaultSlug)
                ->orWhere('subdomain', $defaultSlug)
                ->orWhere('domain', $defaultSlug)
                ->first();

            if ($tenant) {
                return $tenant;
            }
        }

        if (config('tenant.fallback_to_first_active', false)) {
            return Tenant::where('status', 'active')->orderBy('id')->first();
        }

        return null;
    }

    /**
     * Generate tenant specific URL.
     */
    public function getTenantUrl(Tenant $tenant, string $path = '/'): string
    {
        $path = '/' . ltrim($path, '/');
        $appUrl = config('app.url', url('/'));
        $parsed = parse_url($appUrl);
        $scheme = $parsed['scheme'] ?? 'http';
        $host = $parsed['host'] ?? 'localhost';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';

        // Custom domain varsa onu kullan
        if (!empty($tenant->domain)) {
            return $scheme . '://' . $tenant->domain . $path;
        }

        // Subdomain mevcut ve ana domain localhost değilse subdomain kullan
        if (!empty($tenant->subdomain) && !in_array($host, ['localhost', '127.0.0.1'])) {
            return $scheme . '://' . $tenant->subdomain . '.' . $host . $port . $path;
        }

        // Lokal geliştirme için query param fallback
        $base = rtrim($appUrl, '/');
        $separator = str_contains($path, '?') ? '&' : '?';
        $identifier = $tenant->subdomain ?? $tenant->slug ?? $tenant->id;

        return $base . $path . $separator . 'tenant=' . $identifier;
    }
}



