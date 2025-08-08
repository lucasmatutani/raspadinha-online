<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Verificar se o usuário é admin (você pode ajustar esta lógica conforme necessário)
        // Por exemplo, verificar se o ID do usuário está em uma lista de admins
        // ou se existe um campo 'is_admin' na tabela users
        $adminIds = [1]; // IDs dos usuários administradores
        
        if (!in_array(auth()->id(), $adminIds)) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}