<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        $impersonatingTenantId = $request->session()->get('impersonating_tenant_id');
        $impersonatingTenant = null;

        if ($impersonatingTenantId) {
            $impersonatingTenant = \App\Models\Tenant::find($impersonatingTenantId);
        }

        return [
            ...parent::share($request),
            'session' => [
                'status' => fn () => $request->session()->get('status'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => $request->user(),
                'student' => $request->user('student'),
            ],
            'impersonating' => $impersonatingTenant ? [
                'tenant_id' => $impersonatingTenant->id,
                'tenant_name' => $impersonatingTenant->name,
            ] : null,
        ];
    }
}
