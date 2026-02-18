<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllowRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Comma-separated or individual role names to allow
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Eager load roles dan positions
        $user->load('roles.positions');
        // Normalize input roles/positions
        $allowedRoles = $this->normalizeRoles($roles);
        // Cek apakah ada role dan posisi yang cocok **bersamaan**
        $hasAccess = $user->roles->contains(function ($role) use ($allowedRoles) {
            $roleName = strtoupper($role->name);
            $positions = $role->positions->pluck('position')->map(fn($p) => strtoupper($p))->toArray();

            // Role harus ada di allowedRoles **dan** posisi minimal satu cocok
            return in_array($roleName, $allowedRoles) && count(array_intersect($positions, $allowedRoles)) > 0;
        });

        if (!$hasAccess) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }

    /**
     * Normalize role/position names menjadi array uppercase
     */
    private function normalizeRoles(array $roles): array
    {
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = array_map('trim', explode(',', $roles[0]));
        }
        return array_map(fn($r) => strtoupper($r), $roles);
    }
}
