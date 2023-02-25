<?php

/**
 * @file index.php The manin admin view file that will show the actual library file manager
 *
 * */

// Security check
if (!\defined('ABSPATH')) {
    exit();
}

global $FileManager;
?>
<?php require_once 'header.php'; ?>

<div class='fm-container'>

    <div class='col-main'>
        <?php
        $review = new FMReviewClass();
$review->render();
?>
        <div class='gb-fm-row'>

            <?php require 'files.php'; ?>

        </div>

    </div>

    <?php
     if (!\defined('BFM_CLIENT_COMPLAIN')) {
         include_once 'sidebar.php';
     }
?>

</div>

<?php
require_once 'footer.php';