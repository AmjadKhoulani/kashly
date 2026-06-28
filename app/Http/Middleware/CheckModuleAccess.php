<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!auth()->check() || !auth()->user()->isModuleActive($module)) {
            return redirect()->route('dashboard')->with('error', 'هذه المنظومة غير مفعلة في حسابك حالياً. يمكنك تفعيلها من إعدادات المنظومات.');
        }

        return $next($request);
    }
}
