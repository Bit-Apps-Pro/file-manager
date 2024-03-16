<?php

namespace BitApps\FM\Http\Middleware;

use BitApps\FM\Config;
use BitApps\WPKit\Http\Request\Request;

final class NonceCheckerMiddleware
{
    public function handle(Request $request, $role, ...$params)
    {
        $nonceKey = $role === 'admin' ? Config::withPrefix('nonce') : Config::withPrefix('public_nonce');
        if (
            ! $request->has('nonce')
            || !(
                $request->has('nonce')
                && wp_verify_nonce(sanitize_key($request->nonce), $nonceKey)
            )
        ) {
            $response = \in_array($request->getRoute()->getPath(), ['connector', 'connector_front'])
            ? wp_json_encode(['error' => __('Token expired. please reload the page', 'file-manager')])
            : wp_json_encode(
                [
                    'message' => __('Token expired. please reload the page', 'file-manager'),
                    'code'    => 'INVALID_NONCE',
                    'status'  => 'error',
                ]
            );

            echo $response;
            wp_die();
        }

        return true;
    }
}
