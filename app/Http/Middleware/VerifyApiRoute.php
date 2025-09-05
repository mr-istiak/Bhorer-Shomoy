<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestToken = $request->header('Authorization');
        $requestToken = str_replace('Bearer ', '', $requestToken);

        $token = config('app.url') . config('app.key') . config('app.parent_url');
        if(Hash::check($token, $requestToken)) {
            return $next($request);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
