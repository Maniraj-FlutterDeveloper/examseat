<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Check if student is authenticated
        if (!Auth::guard('student')->check()) {
            return redirect()->route('mobile.login');
        }

        $student = Auth::guard('student')->user();

        // Check if student is active
        if (!$student->isActive()) {
            Auth::guard('student')->logout();
            return redirect()->route('mobile.login')
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        // Check if student has the required permission
        if (!$student->hasPermission($permission)) {
            abort(403, 'Unauthorized action. You do not have the necessary permissions.');
        }

        return $next($request);
    }
}

