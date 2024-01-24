<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Config;
use BitApps\FM\Http\Requests\FileManagerRequest;
use BitApps\FM\Plugin;
use BitApps\FM\Providers\FileManager\FileManagerProvider;
use BitApps\FM\Providers\FileManager\FileRoot;
use BitApps\FM\Providers\FileManager\Options;
use BitApps\WPKit\Http\Response;
use BitApps\WPKit\Utils\Capabilities;
use Exception;

final class FileManagerController
{
    // Request Data {"action":"bit_fm_theme","nonce":"db9a06c6de","theme":"material-default"}
    public function changeThemes(FileManagerRequest $request)
    {
        $reqData = $request->validated();

        if (\in_array('theme',$reqData) && Capabilities::filter(Config::VAR_PREFIX . 'user_can_change_theme')) {
            $prefs = Plugin::instance()->preferences();
            $prefs->setTheme(sanitize_text_field($reqData->theme));
            if ($prefs->saveOptions()) {
                return Response::message(__('Theme updated successfully', 'file-manger'));
            }
        }
    }

    /**
     * File Manager connector function
     *
     * @throws Exception
     */
    public function connector()
    {
        try {
            $finderProvider = new FileManagerProvider($this->getFinderOptions());
            $finderProvider->getFinder()->run();
        } catch (Exception $th) {
            // phpcs:ignore
            echo wp_json_encode(['error' => $th->getMessage()]);
        }

        wp_die();
    }

    public function getFinderOptions()
    {
        $finderOptions = new Options(is_user_logged_in() && \defined('WP_DEBUG') && WP_DEBUG);

        $finderOptions->setBind(
            'put.pre',
            [
                Plugin::instance()->fileEditValidator(),
                'validate',
            ]
        );

        $finderOptions->setBind(
            'get.pre file.pre archive.pre back.pre chmod.pre colwidth.pre copy.pre cut.pre duplicate.pre editor.pre put.pre
             extract.pre forward.pre fullscreen.pre getfile.pre help.pre home.pre info.pre mkdir.pre mkfile.pre
             netmount.pre netunmount.pre open.pre opendir.pre paste.pre places.pre quicklook.pre reload.pre
             rename.pre resize.pre restore.pre rm.pre search.pre sort.pre up.pre upload.pre view.pre zipdl.pre
             tree.pre parents.pre ls.pre tmb.pre size.pre dim.pre',
            [
                Plugin::instance()->accessControl(),
                'checkPermission',
            ]
        );

        $finderOptions->setBind(
            'upload',
            [Plugin::instance()->mediaSyncs(), 'onFileUpload']
        );

        // 'zipdl.pre file.pre rename.pre put.pre upload.pre',

        $finderOptions->setBind(
            'zipdl.pre file.pre rename.pre put.pre upload.pre',
            [Plugin::instance()->logger(), 'log']
        );

        foreach ($this->getFileRoots() as $root) {
            $finderOptions->setRoot($root);
        }

        return $finderOptions;
    }

    public function getFileRoots()
    {
        $mimes                 = Plugin::instance()->mimes()->getTypes();
        $preferences           = Plugin::instance()->preferences();
        $accessControlProvider = Plugin::instance()->accessControl();
        $permissions           = Plugin::instance()->permissions();

        $path     = $permissions->getPath();
        $baseRoot = new FileRoot(
            $path,
            $permissions->getURL(),
            $permissions->getVolumeAlias()
        );

        if ($permissions->currentUserRole() !== 'administrator') {
            $mimes         = $permissions->getEnabledFileType();
            $maxUploadSize = $permissions->getMaximumUploadSize();
            $baseRoot->setUploadMaxSize($maxUploadSize == 0 ? 0 : $maxUploadSize . 'M');
            $denyUploadType = array_diff(Plugin::instance()->mimes()->getTypes(), $mimes);
            $baseRoot->setOption('uploadDeny', $denyUploadType);
        }

        if (is_writable(stripslashes($path) . DIRECTORY_SEPARATOR . '.tmbPath')) {
            $baseRoot->setOption('tmbPath', '.tmb');
        }

        $baseRoot->setUploadAllow($mimes);
        $baseRoot->setAccessControl([$accessControlProvider, 'control']);
        $baseRoot->setAcceptedName([$accessControlProvider, 'validateName']);
        $baseRoot->setDisabled($permissions->getDisabledCommand());
        $baseRoot->setWinHashFix(DIRECTORY_SEPARATOR !== '/');

        if (Capabilities::filter(Config::VAR_PREFIX . 'user_can_manage_options')) {
            $baseRoot->setAllowChmodReadOnly(true);
            $baseRoot->setStatOwner(true);
            $baseRoot->setUploadMaxSize(0);
        }

        $roots[] = $baseRoot;

        if ($permissions->currentUserRole() === 'administrator') {
            $baseRoot->setTrashHash($preferences->isTrashAllowed() ? 't1_Lw' : '');

            $mediaRoot = new FileRoot(FM_MEDIA_BASE_DIR_PATH, FM_MEDIA_BASE_DIR_URL, 'Media');
            $mediaRoot->setUploadAllow($mimes);
            $mediaRoot->setAccessControl([$accessControlProvider, 'control']);

            $roots[] = $mediaRoot;

            if ($preferences->isTrashAllowed()) {
                $trashRoot = new FileRoot(FM_TRASH_DIR_PATH, FM_TRASH_TMB_DIR_URL, 'trash', 'Trash');
                $trashRoot->setOption('id', 1);
                $trashRoot->setUploadAllow($mimes);
                $trashRoot->setAccessControl([$accessControlProvider, 'control']);
                $trashRoot->setAcceptedName([$accessControlProvider, 'validateName']);

                $roots[] = $trashRoot;
            }
        }

        return $roots;
    }
}
