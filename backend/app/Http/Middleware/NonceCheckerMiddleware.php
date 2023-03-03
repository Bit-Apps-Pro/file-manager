<?php

namespace BitApps\FM\Http\Middleware;

use BitApps\FM\Core\Http\Request\Request;
use BitApps\FM\Core\Http\Response;

final class NonceCheckerMiddleware
{
    public function handle(Request $request, ...$params)
    {
        if (! $request->has('bfm_nonce') || wp_verify_nonce(sanitize_key($request->bfm_nonce), 'bitflow_noce')) {
            return Response::error('Invalid token')->httpStatus(411);
        }

        return true;
    }
}
