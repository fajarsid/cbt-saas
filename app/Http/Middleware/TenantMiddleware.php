<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        // Check if user is authenticated and has a tenant
        if ($request->user() && $request->user()->tenant_id) {
            $tenant = Tenant::find($request->user()->tenant_id);
        }

        // Check if student is authenticated and has a tenant
        if (!$tenant && $request->user('student') && $request->user('student')->tenant_id) {
            $tenant = Tenant::find($request->user('student')->tenant_id);
        }

        // If no tenant found, check subdomain or path
        if (!$tenant) {
            $tenant = $this->resolveTenantFromRequest($request);
        }

        if ($tenant) {
            // Check if tenant is active
            if (!$tenant->isActive()) {
                abort(403, 'Organization is suspended or inactive.');
            }

            // Bind tenant to container
            app()->instance('tenant', $tenant);

            // Share tenant with views
            view()->share('currentTenant', $tenant);
        }

        return $next($request);
    }

    protected function resolveTenantFromRequest(Request $request): ?Tenant
    {
        // Try to resolve from subdomain
        $host = $request->getHost();
        $parts = explode('.', $host);

        if (count($parts) >= 3) {
            $subdomain = $parts[0];
            $tenant = Tenant::where('slug', $subdomain)
                ->orWhere('domain', $host)
                ->first();

            if ($tenant) {
                return $tenant;
            }
        }

        // Try to resolve from custom domain
        return Tenant::where('domain', $host)->first();
    }
}
