<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Request\Request;
use BitApps\FM\Dependencies\BitApps\WPKit\Http\Response;
use BitApps\FM\Plugin;
use BitApps\FM\Providers\PreferenceProvider;

final class SettingsController
{
    public PreferenceProvider $preferenceProvider;

    public function __construct()
    {
        $this->preferenceProvider = Plugin::instance()->preferences();
    }

    public function get()
    {
        return Response::success(
            [
                'settings'  => $this->preferenceProvider->all(),
                'defaults'  => [
                    'path' => esc_html(ABSPATH),
                    'url'  => esc_html(site_url()),
                ],
                'themes'    => $this->preferenceProvider->getThemes(),
                'languages' => $this->preferenceProvider->getLanguages(),
            ]
        );
    }

    public function update(Request $request)
    {
        if (true) {
            return Response::success([])->message('Settings updated successfully');
        }

        return Response::error([])->message('failed to update settings');
    }
}
