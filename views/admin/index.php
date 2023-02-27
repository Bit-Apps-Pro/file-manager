<?php

/**
 * The main admin view file that will show the actual library file manager
 * */

use BitApps\FM\Views\Review;

// Security check
if (!\defined('ABSPATH')) {
    exit();
}
?>
<?php

require_once 'header.php';
$review = new Review();
$review->render();
?>
<div class='fm-container'>

    <div class='col-main'>
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
