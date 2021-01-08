<?php
/**
 *
 * @file __init__.php
 * @description Loads all the file necessary in the inc folder
 *
 * */

// Security check
defined('ABSPATH') || die();

require_once('logger.php');
require_once('class.logger.php');
require_once('migrate.php');
require_once('active-deactive.php');
require_once('class.review.php');
require_once('class.access-control.php');
require_once('class.php.syntax.checker.php');
require_once('class.mime.php');
require_once('class.media-sync.php');
