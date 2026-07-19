<?php

namespace EloquentWorks\Persona\Http\Middleware;

use Closure;
use EloquentWorks\Persona\Models\Persona;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure that the requested persona is visible.
 */
final class EnsurePersonaIsVisible
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the persona from the route parameters
        $persona = $request->route('persona');

        // If the persona is not visible, abort with a 404 response
        if ($persona instanceof Persona && ! $persona->isVisible()) {
            abort(404);
        }

        // Continue processing the request
        return $next($request);
    }
}
