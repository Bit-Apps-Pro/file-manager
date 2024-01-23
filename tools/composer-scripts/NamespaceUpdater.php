<?php

namespace BitApps\FM\Tools\ComposerScripts;

final class NamespaceUpdater
{
    public static string $outputDir;

    public static array $composerConfig;

    public static \Composer\Script\Event $event;

    public static function preInstall(\Composer\Script\Event $event)
    {
        self::$event = $event;

        self::clearStraussOutputDir();
    }

    public static function preUpdate(\Composer\Script\Event $event)
    {
        self::$event = $event;

        self::clearStraussOutputDir();
    }

    public static function postInstall(\Composer\Script\Event $event)
    {
        self::$event = $event;

        self::runStrauss();
    }

    public static function postUpdate(\Composer\Script\Event $event)
    {
        self::$event = $event;

        self::runStrauss();
    }

    public static function runStrauss()
    {
        self::$event->getIO()->write('Running Strauss...');
        if (!self::getStraussOption('target_directory')) {
            self::$event->getIO()->write('Strauss config does not exists in composer.json');

            return;
        }
        $composerConfig = self::getComposerConfig();
        if (self::$event->isDevMode()) {
            $composerConfig['extra']['strauss']['delete_vendor_packages'] = false;
            $composerConfig['extra']['strauss']['delete_vendor_files']    = false;
            self::updateComposerConfig($composerConfig);
            self::$event->getIO()->write(shell_exec('composer strauss; composer dump-autoload'));
        } else {
            unset($composerConfig['autoload']['psr-4'][__NAMESPACE__ . '\\']);
            self::updateComposerConfig($composerConfig);
            self::deleteStaticFiles();
            self::$event->getIO()->write(shell_exec('command -v strauss || composer global require --dev brianhenryie/strauss'));
            self::$event->getIO()->write(shell_exec('strauss; composer dump-autoload'));
            self::removeEmptyFolderFromVendor();
        }
        self::copyStaticFiles();
        self::resetComposerConfig();
    }

    public static function clearStraussOutputDir()
    {
        if ($outputDir = self::getOutputPath()) {
            if (file_exists($outputDir)) {
                self::removeFileRecursively($outputDir);
            }

            mkdir($outputDir);
        }
    }

    public static function getStraussOption($optionName)
    {
        $composerConfig = self::getComposerConfig();

        if (!isset($composerConfig['extra'])
            || !isset($composerConfig['extra']['strauss'])
            || !isset($composerConfig['extra']['strauss'][$optionName])
        ) {
            return false;
        }

        return $composerConfig['extra']['strauss'][$optionName];
    }

    public static function getComposerConfig(): array
    {
        if (!isset(self::$composerConfig)) {
            self::$composerConfig = json_decode(
                file_get_contents(self::getVendorPath() . '/../composer.json'),
                true
            );
        }

        return self::$composerConfig;
    }

    private static function copyStaticFiles()
    {
        if (($staticFiles = self::getStraussOption('copy_static_files')) && \is_array($staticFiles)) {
            foreach ($staticFiles as $file) {
                $sourceFile      = self::getPrefixedPath($file);
                $destinationFile = str_replace(self::getVendorPath(), self::getOutputPath(), $sourceFile);
                self::copyFileRecursively($sourceFile, $destinationFile);
            }
        }
    }

    private static function deleteStaticFiles()
    {
        if (($staticFiles = self::getStraussOption('delete_static_files')) && \is_array($staticFiles)) {
            foreach ($staticFiles as $file) {
                self::removeFileRecursively(self::getPrefixedPath($file));
            }
        }
    }

    private static function getVendorPath(): string
    {
        return self::$event->getComposer()->getConfig()->get('vendor-dir');
    }

    private static function getOutputPath(): string
    {
        if (!isset(self::$outputDir) && ($targetDirectory = self::getStraussOption('target_directory'))) {
            self::$outputDir = \dirname(self::$event->getComposer()->getConfig()->get('vendor-dir'))
                . DIRECTORY_SEPARATOR
                . $targetDirectory;
        }

        return self::$outputDir;
    }

    private static function getPrefixedPath($path): string
    {
        return strpos($path, self::getVendorPath()) === false
        ? self::getVendorPath() . DIRECTORY_SEPARATOR . $path : $path;
    }

    private static function updateComposerConfig(array $config)
    {
        file_put_contents(
            self::getVendorPath() . '/../composer.json',
            preg_replace(
                '/^(  +?)\\1(?=[^ ])/m',
                '$1',
                json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            )
        );
    }

    private static function resetComposerConfig()
    {
        self::updateComposerConfig(self::getComposerConfig());
    }

    private static function removeEmptyFolderFromVendor()
    {
        $vendorPath = self::getVendorPath();
        foreach (scandir(self::getOutputPath()) as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }
            $folderInVendor = $vendorPath . DIRECTORY_SEPARATOR . $folder;
            if (file_exists($folderInVendor) && \count(scandir($folderInVendor)) === 2) {
                rmdir($folderInVendor);
            }
        }
    }

    private static function copyFileRecursively($sourceFile, $destinationFile)
    {
        if (file_exists($destinationFile)) {
            self::removeFileRecursively($destinationFile);
        }
        if (is_dir($sourceFile)) {
            mkdir($destinationFile);
            $files = scandir($sourceFile);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    self::copyFileRecursively("{$sourceFile}/{$file}", "{$destinationFile}/{$file}");
                }
            }
        } elseif (file_exists($sourceFile)) {
            copy($sourceFile, $destinationFile);
        }
    }

    private static function removeFileRecursively($filePath)
    {
        if (is_dir($filePath)) {
            $files = scandir($filePath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    self::removeFileRecursively("{$filePath}/{$file}");
                }
            }
            rmdir($filePath);
        } elseif (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
