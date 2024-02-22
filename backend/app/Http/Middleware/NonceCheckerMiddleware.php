<?php

namespace BitApps\FM\Http\Middleware;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Request\Request;

final class NonceCheckerMiddleware
{
    public function handle(Request $request, $role, ...$params)
    {
        if (
            ! $request->has('nonce')
            || !(
                $request->has('nonce')
                && wp_verify_nonce(sanitize_key($request->nonce), 'bfm_nonce')
            )
        ) {
            echo wp_json_encode(
                [
                    'message' => __('Token expired. please reload the page', 'file-manager'),
                    'code'    => 'INVALID_NONCE',
                    'status'  => 'error',
                ]
            );
            wp_die();
        }

        return true;
    }
}
