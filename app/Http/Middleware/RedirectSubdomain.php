<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract the host and remove protocol
        $currentHost = $request->getHost();
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? config('app.url');

        if ($currentHost !== $mainDomain) {
            return redirect()->to(config('app.url') . $request->getRequestUri());
        }

        return $next($request);
    }
}
