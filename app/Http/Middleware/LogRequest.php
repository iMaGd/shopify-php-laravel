<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed   ...$options
     *
     * @return mixed
     */
    public function handle( Request $request, Closure $next, ...$options )
    {

        $requestReport = [
            'url'        => $request->fullUrl(),
            'method'     => $request->method(),
            'path'       => $request->path(),
            'ip'         => $request->ip(),
            'bearer'     => $request->bearerToken(),
            'attributes' => $request->attributes->all(),
            'body'       => $request->all(),
        ];

        if( empty( $options ) ){
            $options = ['session', 'cookie'];
        }

        if( in_array( 'header', $options ) ){
            $requestReport['header'] = $request->headers->all();
        }
        if( in_array( 'session', $options ) ){
            $requestReport['session'] = $request->session()->all();
        }
        if( in_array( 'cookie', $options ) ){
            $requestReport['cookies'] = $request->cookies->all();
        }

        $log = "\n". json_encode( $requestReport ,JSON_PRETTY_PRINT ) . "\n==================================";

        Log::channel('request')->debug( $log );

        return $next( $request );
    }
}
