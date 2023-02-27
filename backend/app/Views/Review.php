<?php

namespace BitApps\FM\Views;

use function BitApps\FM\Functions\view;

use BitApps\FM\Plugin;

use BitApps\FM\Providers\ReviewProvider;

/**
 * The admin page review block
 */
class Review
{
    public function __construct()
    {
        $this->processReview();
    }

    public function render()
    {
        if (Plugin::instance()->reviewNotifier()->isShowAble()) {
            return view('admin.review');
        }
    }

    private function processReview()
    {
        $reviewStatus = isset($_GET['fm-review-status']) ? sanitize_text_field($_GET['fm-review-status']) : '';
        if (\in_array($reviewStatus, ReviewProvider::$status)) {
            if (ReviewProvider::SUCCESSFUL === $reviewStatus) {
                Plugin::instance()->reviewNotifier()->setStatus(ReviewProvider::SUCCESSFUL, time());
                ?>
                    <script>
                    window.location = "https://wordpress.org/support/plugin/file-manager/reviews/#new-post";
                    </script>
                    <?php
            } else {
                Plugin::instance()->reviewNotifier()->setStatus($reviewStatus, time());
            }
        }
    }
}
