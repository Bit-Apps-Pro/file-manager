<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Config;
use BitApps\FM\Core\Http\Request\Request;
use BitApps\FM\Core\Http\Response;
use BitApps\FM\Core\Utils\Capabilities;
use BitApps\FM\Plugin;
use BitApps\FM\Providers\FileManager\FileManagerProvider;
use BitApps\FM\Providers\FileManager\FileRoot;
use BitApps\FM\Providers\FileManager\Options;
use Exception;

final class FileManagerController
{
    public function changeTheme(Request $request)
    {
        if ($request->has('theme') && Capabilities::filter(Config::VAR_PREFIX . 'user_can_change_theme')) {
            $prefs = Plugin::instance()->preferences();
            $prefs->setTheme(sanitize_text_field($request->theme));
            if ($prefs->saveOptions()) {
                return Response::message(__('Theme updated successfully', 'file-manger'));
            }
        }

        return Response::message(__('Failed to update theme', 'file-manger'));
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
        $finderOptions = new Options(WP_DEBUG);

        $finderOptions->setBind(
            'put.pre',
            [
                Plugin::instance()->fileEditValidator(),
                'validate',
            ]
        );

        $finderOptions->setBind(
            'archive.pre back.pre chmod.pre colwidth.pre copy.pre cut.pre duplicate.pre editor.pre put.pre
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
        $mime                  = Plugin::instance()->mimes();
        $preferences           = Plugin::instance()->preferences();
        $accessControlProvider = Plugin::instance()->accessControl();
        $permissions           = Plugin::instance()->permissions();

        $path     = $permissions->getPath();
        $baseRoot = new FileRoot(
            $path,
            $permissions->getURL(),
            $permissions->getVolumeAlias()
        );

        if (is_writable(stripslashes($path) . DIRECTORY_SEPARATOR . '.tmbPath')) {
            $baseRoot->setOption('tmbPath', '.tmb');
        }

        $baseRoot->setUploadAllow($mime->getTypes());
        $baseRoot->setAccessControl([$accessControlProvider, 'control']);
        $baseRoot->setAcceptedName([$accessControlProvider, 'validateName']);
        $baseRoot->setDisabled($permissions->getDisabledCommand());
        $baseRoot->setTrashHash($preferences->isTrashAllowed() ? 't1_Lw' : '');
        $baseRoot->setWinHashFix(DIRECTORY_SEPARATOR !== '/');

        if (Capabilities::filter(Config::VAR_PREFIX . 'user_can_manage_options')) {
            $baseRoot->setAllowChmodReadOnly(true);
            $baseRoot->setStatOwner(true);
            $baseRoot->setUploadMaxSize(0);
        }

        $roots[] = $baseRoot;

        if ($permissions->currentUserRole() === 'administrator') {
            $mediaRoot = new FileRoot(FM_MEDIA_BASE_DIR_PATH, FM_MEDIA_BASE_DIR_URL, 'Media');
            $mediaRoot->setUploadAllow($mime->getTypes());
            $mediaRoot->setAccessControl([$accessControlProvider, 'control']);

            $roots[] = $mediaRoot;

            if ($preferences->isTrashAllowed()) {
                $trashRoot = new FileRoot(FM_TRASH_DIR_PATH, FM_TRASH_TMB_DIR_URL, 'trash', 'Trash');
                $trashRoot->setOption('id', 1);
                $trashRoot->setUploadAllow($mime->getTypes());
                $trashRoot->setAccessControl([$accessControlProvider, 'control']);
                $trashRoot->setAcceptedName([$accessControlProvider, 'validateName']);

                $roots[] = $trashRoot;
            }
        }

        return $roots;
    }
}
