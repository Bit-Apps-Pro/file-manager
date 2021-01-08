<?php
/**
 * 
 * @file donate.php Donate links will go here
 * 
 * */

// Security Check
defined('ABSPATH') || die();
?>
<style type="text/css">
	.fm-donate{
		padding: 1.5rem;
	}
</style>
<div class='fm-donate'>
<h2><?php _e("Buy me a coffee", 'file-manager'); ?></h2>
<p>
	<?php _e("It takes time, effort and investment to develop, maintain and support a plugin. If you want us to continue further work on the plugin, please support us with your donation. Even a small amount of donation helps.", 'file-manager'); ?>
</p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="BUQANHSTBS48Q">
<table>
<tr><td><input type="hidden" name="on0" value="Buy me a Coffee"><?php _e("Buy me a Coffee", 'file-manager'); ?></td></tr><tr><td><select name="os0">
	<option value="Coffee">Coffee $5.00 USD</option>
	<option value="Coffee">Coffee $10.00 USD</option>
	<option value="Coffee">Coffee $15.00 USD</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="USD">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynow_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</div>
