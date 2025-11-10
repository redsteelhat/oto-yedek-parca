<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Super admin için scope uygulama
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            return;
        }

        // Tenant ID'yi al
        $tenantId = app(\App\Services\TenantService::class)->getCurrentTenantId();

        if ($tenantId) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        }
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenantScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        $builder->macro('withTenantScope', function (Builder $builder, $tenantId = null) {
            $tenantId = $tenantId ?? app(\App\Services\TenantService::class)->getCurrentTenantId();
            
            if ($tenantId) {
                return $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenantId);
            }

            return $builder;
        });
    }
}



