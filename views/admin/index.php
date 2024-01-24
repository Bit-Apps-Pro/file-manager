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
?>
<div class="fm-promo-feedback">
<?php
$review = new Review();
$review->render();
?>
<a href="https://bitapps.pro">
    <img src="<?php echo esc_html(BFM_ASSET_URL);?>img/banner.png" width="800px" height="125px"/>
</a>
</div>
<div class='fm-container'>
    <div class='col-main'>
        <div class='gb-fm-row'>
            <?php view('finder'); ?>
        </div>
    </div>

    <?php
     if (!\defined('BFM_CLIENT_COMPLAIN')) {
        //  view('admin.sidebar');
     }
?>

</div>

<?php
require_once 'footer.php';
