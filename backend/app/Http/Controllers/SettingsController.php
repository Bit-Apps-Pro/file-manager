<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Response;
use BitApps\FM\Http\Requests\Settings\SettingsRequest;
use BitApps\FM\Http\Requests\Settings\SettingsUpdateRequest;
use BitApps\FM\Plugin;
use BitApps\FM\Providers\PreferenceProvider;

final class SettingsController
{
    public PreferenceProvider $preferenceProvider;

    public function __construct()
    {
        $this->preferenceProvider = Plugin::instance()->preferences();
    }

    public function get(SettingsRequest $request)
    {
        return Response::success(
            [
                'settings'  => $this->preferenceProvider->all(),
                'defaults'  => $this->preferenceProvider->defaultPrefs(),
                'themes'    => $this->preferenceProvider->getThemes(),
                'languages' => $this->preferenceProvider->getLanguages(),
            ]
        );
    }

    public function update(SettingsUpdateRequest $request)
    {
        $updatedSettings = $request->validated();

        $settingsService = Plugin::instance()->preferences();

        $settingsService->setLinkPathVisibility($updatedSettings['show_url_path']);
        $settingsService->setVisibilityOfHiddenFile($updatedSettings['show_hidden_files']);
        $settingsService->setPermissionForTrashCreation($updatedSettings['create_trash_files_folders']);
        $settingsService->setPermissionForHiddenFolderCreation($updatedSettings['create_hidden_files_folders']);
        $settingsService->setRememberLastDir($updatedSettings['remember_last_dir']);
        $settingsService->setClearHistoryOnReload($updatedSettings['clear_history_on_reload']);
        $settingsService->setRootVolumeName($updatedSettings['root_folder_name']);
        $settingsService->setTheme($updatedSettings['theme']);
        $settingsService->setLang($updatedSettings['language']);
        $settingsService->setViewType($updatedSettings['default_view_type']);
        $settingsService->setRootPath($updatedSettings['root_folder_path']);
        $settingsService->setRootUrl($updatedSettings['root_folder_url']);
        $settingsService->setWidth($updatedSettings['size']['width']);
        $settingsService->setHeight($updatedSettings['size']['height']);
        $settingsService->setUiOptions($updatedSettings['display_ui_options']);

        if ($settingsService->saveOptions()) {
            return Response::success([])->message('Settings updated successfully');
        }

        return Response::error([])->message('failed to update settings');
    }
}
