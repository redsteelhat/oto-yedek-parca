<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantService;
use App\Models\Tenant;

class TenantMiddleware
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Super admin için bypass (admin subdomain veya manage subdomain veya /super-admin path)
        $host = $request->getHost();
        $isSuperAdminRoute = $this->isSuperAdminRoute($host, $request->path());

        if ($isSuperAdminRoute || str_starts_with($request->path(), 'super-admin')) {
            $this->tenantService->clearTenant();
            return $next($request);
        }

        // Subdomain veya domain üzerinden tenant'ı bul
        $subdomain = $this->tenantService->getSubdomainFromRequest();
        $tenant = null;

        if ($subdomain) {
            $tenant = Tenant::where('subdomain', $subdomain)
                ->orWhere('domain', $host)
                ->first();
        } else {
            // Domain eşleşmesi (custom domain)
            $tenant = Tenant::where('domain', $host)->first();

            // Eğer domain match yoksa public site (aggregator)
            if (!$tenant) {
                $this->tenantService->clearTenant();
                view()->share('currentTenant', null);
                return $next($request);
            }
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Tenant aktif mi kontrol et
        if (!$tenant->isActive()) {
            abort(403, 'Tenant is not active');
        }

        // Abonelik kontrolü
        if (!$tenant->hasActiveSubscription()) {
            abort(403, 'Tenant subscription has expired');
        }

        // Tenant'ı ayarla
        $this->tenantService->setTenant($tenant);

        // Tenant bilgisini view'lara aktar
        view()->share('currentTenant', $tenant);

        return $next($request);
    }

    /**
     * Check if this is a super admin route.
     */
    protected function isSuperAdminRoute(string $host, string $path): bool
    {
        // Admin subdomain kontrolü
        $parts = explode('.', $host);
        if (count($parts) >= 3 && in_array($parts[0], ['admin', 'manage', 'super'])) {
            return true;
        }

        // Path-based kontrolü (eğer super admin routes /super-admin ile başlıyorsa)
        if (str_starts_with($path, '/super-admin')) {
            return true;
        }

        return false;
    }
}

