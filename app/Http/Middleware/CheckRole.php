<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\FirebaseService;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $firestore;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firestore = $firebaseService->getFirestore();
    }

    public function handle(Request $request, Closure $next, $role)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['error' => 'Utilisateur non authentifié'], 401);
    }

    // Les données de l'utilisateur sont déjà chargées par le guard
    if (!isset($user->roleId) || $user->roleId !== $role) {
        return response()->json(['error' => 'Accès non autorisé'], 403);
    }

    return $next($request);
}
}
