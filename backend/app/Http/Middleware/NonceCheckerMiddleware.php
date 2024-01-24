<?php

namespace BitApps\FM\Http\Middleware;

use BitApps\WPKit\Http\Request\Request;

final class NonceCheckerMiddleware
{
    public function handle(Request $request, ...$params)
    {
        if (
            ! $request->has('nonce')
            || !(
                $request->has('nonce')
                && wp_verify_nonce(sanitize_key($request->nonce), 'bfm_nonce')
            )
        ) {
            echo json_encode(['error' => [__('Token expired. please reload the page', 'file-manager')]]);
            wp_die();
        }

        return true;
    }
}
