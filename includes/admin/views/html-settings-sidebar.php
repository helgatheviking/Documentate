<?php
/**
 * Knowledgebase Settings Sidebar
 *
 * @version     0.1-beta
 * @author      helgatheviking
 */

?>
<div class="documentate_admin_sidebar metabox-holder">
	<div class="postbox documentate_donation">
		<h3 class="hndle"><span><?php _e('Help Improve This Plugin!','documentate') ?></span></h3>
		<div class="inside">
			<?php _e('Enjoyed this plugin? All donations are used to improve and further develop this plugin. Thanks for your contribution.','documentate') ?>
				
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="A74K2K689DWTY">
					<input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
			</form>

		<p><?php printf( __( 'You can also help by %srating this plugin on wordpress.org%s', 'documentate'), 
					'<a href="http://wordpress.org/support/view/plugin-reviews/documentate" target="_blank">',
					'</a>' ); ?></p>
		</div>
	</div>

	<div class="postbox documentate_donation">
		<h3 class="hndle"><span><?php _e('Need Support?', 'documentate') ?></span></h3>
		<div class="inside">
			<?php printf( __('Check out the %sFAQs%s and %sSupport Forums%s.', 'documentate'),
				'<a href="http://wordpress.org/plugins/documentate/faq" target="_blank">',
				'</a>',
				'<a href="http://wordpress.org/support/plugin/documentate" target="_blank">',
				'</a>' ); ?>
		</div>
	</div>
			
</div>