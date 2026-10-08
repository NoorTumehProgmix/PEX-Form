<?php

namespace Juzaweb\Frontend\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * TEMP — delete this controller after Cloudflare / real client IP is verified.
 */
class TempIpCheckController extends Controller
{
    public function show(): View
    {
        return view('frontend::temp-ip-check');
    }

    public function check(Request $request): JsonResponse
    {
        return response()->json([
            'laravel_ip' => $request->ip(),
            'get_client_ip' => get_client_ip(),
            'remote_addr' => $request->server('REMOTE_ADDR'),
            'cf_connecting_ip' => $request->header('CF-Connecting-IP'),
            'x_forwarded_for' => $request->header('X-Forwarded-For'),
            'x_real_ip' => $request->header('X-Real-IP'),
            'checked_at' => now()->toIso8601String(),
        ]);
    }
}
