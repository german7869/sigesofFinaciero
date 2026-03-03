<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // API routes should never redirect – return null so the framework
        // renders the 401 JSON response via the exception handler.
        if ($request->is('api/*') || $request->expectsJson()) {
            return null;
        }

        return null;
    }
}
