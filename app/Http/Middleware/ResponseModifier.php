<?php

namespace App\Http\Middleware;

use Closure;
use Helpers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseModifier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $data = encryptResponse(config('custom.encrypt_decrypt_key_app'), config('custom.encrypt_decrypt_key_app_iv_app'), $response->getContent());
        $response->setContent($data);

        // Add CORS headers to the response
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        return $response;
    }

}
