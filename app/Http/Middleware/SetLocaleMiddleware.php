<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle incoming request and bind the active locale from session or query param.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang') && in_array($request->query('lang'), ['id', 'en'])) {
            session(['locale' => $request->query('lang')]);
        }

        $locale = session('locale', config('app.locale', 'id'));

        if (!in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}

