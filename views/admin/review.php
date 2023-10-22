<?php

\defined('ABSPATH') || exit();
?>
<div class="gb-fm-row review-block">
  <div class="message">
    <?php
        esc_html_e(
            'We are continuously developing and maintaining the plugin.If you like our plugin you can post a review.
       It is very much appreciated.',
            'file-manager'
        );
?>
  </div>
  <div class="actions">
      <a
      target="_blank"
      href="admin.php?page=file-manager&fm-review-status=review-successful"
      class="btn btn-review"
      title="<?php esc_html_e('Leave us a review.', 'file-manager'); ?>"
      >
      <?php esc_html_e('I like your plugin!', 'file-manager'); ?>
    </a>
    <a
      href="admin.php?page=file-manager&fm-review-status=remind-me-later"
      class="btn"
      title="<?php esc_html_e('Remind me later.', 'file-manager'); ?>">
      <?php esc_html_e("I don't have time right now.", 'file-manager'); ?>
    </a>
    <a
      href="admin.php?page=file-manager&fm-review-status=not-interested"
      class="btn btn-not-interested"
      title="<?php esc_html_e("Don't ask again.", 'file-manager'); ?>">
      <?php esc_html_e("I don't care!", 'file-manager'); ?>
    </a>
  </div>
</div>