<?php

namespace MobicardApi\ScanApi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateScanApiConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requiredConfig = ['version', 'mode', 'merchant_id', 'api_key', 'secret_key'];

        foreach ($requiredConfig as $config) {
            if (!config('scanapi.' . $config)) {
                return redirect()->back()->withErrors([
                    'scanapi' => "ScanAPI configuration '{$config}' is missing. Please check your .env file."
                ]);
            }
        }

        return $next($request);
    }
}
