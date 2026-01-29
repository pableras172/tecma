<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if($user) {
            \Log::info('User logged in', [           
                'user_id' =>  $user->id,
                'user_email' =>  $user->email,
            ]);

        }else{
            \Log::info('No user logged in');
        }
        // Si hay un usuario autenticado pero no está activo
        if ($user && !$user->active) {
            // Evitar bucle infinito: si ya estamos en pending-activation, no redirigir
            if (!$request->routeIs('filament.personal.pages.pending-activation')) {
                return redirect()->route('filament.personal.pages.pending-activation');
            }
        }
        
        return $next($request);
    }
}
