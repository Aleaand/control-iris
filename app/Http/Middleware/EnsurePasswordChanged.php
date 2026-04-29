<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Redirect users to password change screen if they have a temporary password.
     * Applies to gestors and clients on their first login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();

        if ($userId) {
            // Verificamos directamente en la base de datos para evitar problemas de caché de sesión
            $mustChange = \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $userId)
                ->value('must_change_password');

            if (!is_null($mustChange)) {
                $allowedRoutes = [
                    'gestor.set-password',
                    'logout',
                ];

                if (!$request->routeIs($allowedRoutes)) {
                    return redirect()->route('gestor.set-password');
                }
            }
        }

        return $next($request);
    }
}
