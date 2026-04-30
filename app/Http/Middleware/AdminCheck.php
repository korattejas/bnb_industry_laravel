<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        if ($user && $user->status == '1' && Auth::guard('admin')->check()) {
            
            // Restrict sales role to only contact-submissions module
            if ($user->role == 'sales') {
                $allowedRoutes = [
                    'admin.contact-submissions.index',
                    'admin.contact-submissions.data', // I'll use the new name I gave it
                    'admin.contact-submissions.status',
                    'admin.contact-submissions.destroy',
                    'admin.contact-submissions.view',
                    'admin.logout',
                    'getDataContactSubmissions', // Fallback for old name if used
                ];

                $currentRoute = $request->route()->getName();

                if (!in_array($currentRoute, $allowedRoutes)) {
                    return redirect()->route('admin.contact-submissions.index');
                }
            }

            return $next($request);
        }

        if ($user && $user->status == '0') {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        }

        return redirect()->route('admin.login');
    }

}
