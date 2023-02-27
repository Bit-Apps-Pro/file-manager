<?php

/**
 *
 * @file __init__.php
 * @description Loads all the file necessary in the inc folder
 *
 * */

// Security check
defined('ABSPATH') || die();

require_once 'logger.php';
require_once 'class.logger.php';
require_once 'class.php.syntax.checker.php';
require_once 'class.mime.php';
require_once 'class.media-sync.php';
require_once 'class.permission-system.php';
require_once 'class.permission-settings.php';
require_once 'class.initializer.php';
