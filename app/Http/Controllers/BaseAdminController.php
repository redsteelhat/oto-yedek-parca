<?php

namespace App\Http\Controllers;

use App\Services\TenantService;
use App\Models\Tenant;

abstract class BaseAdminController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Get current tenant ID.
     */
    protected function getCurrentTenantId(): ?int
    {
        return $this->tenantService->getCurrentTenantId();
    }

    /**
     * Get current tenant.
     */
    protected function getCurrentTenant(): ?Tenant
    {
        return $this->tenantService->getCurrentTenant();
    }

    /**
     * Check if current user is super admin.
     */
    protected function isSuperAdmin(): bool
    {
        return $this->tenantService->isSuperAdmin();
    }

    /**
     * Check tenant limit for products.
     */
    protected function checkProductLimit(): bool
    {
        $tenant = $this->getCurrentTenant();
        
        if (!$tenant) {
            return true; // Super admin, no limit
        }

        return $tenant->canCreateProduct();
    }

    /**
     * Check tenant limit for users.
     */
    protected function checkUserLimit(): bool
    {
        $tenant = $this->getCurrentTenant();
        
        if (!$tenant) {
            return true; // Super admin, no limit
        }

        return $tenant->canCreateUser();
    }
}



