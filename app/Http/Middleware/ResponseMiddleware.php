<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Get the response from the next middleware/handler
        $response = $next($request);

        if ($response instanceof Response) {
            // Add custom headers
            $response->headers->set('X-Custom-Header', 'My Custom Value');

            // Optionally, wrap the response data in a consistent structure
            $data = json_decode($response->getContent(), true); // Get response data as array
            $response->setContent(json_encode([
                'status' => 'success',
                'data' => $data,
                'message' => 'Request was successful',
            ]));
        }

        return $response;
    }
}
