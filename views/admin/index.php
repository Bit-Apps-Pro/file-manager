<?php

/**
 * The main admin view file that will show the actual library file manager
 * */

use function BitApps\FM\Functions\view;

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
            <?php view('finder'); ?>
        </div>
    </div>

    <?php
     if (!\defined('BFM_CLIENT_COMPLAIN')) {
         view('admin.sidebar');
     }
?>

</div>

<?php
require_once 'footer.php';
