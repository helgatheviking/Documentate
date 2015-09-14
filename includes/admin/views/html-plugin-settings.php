<?php
/**
 * Knowledgebase Settings Display Page
 *
 * @version     0.1-beta
 * @author      helgatheviking
 */
?>
<div id="wpbody">
	<div id="wpbody-content">
		<div class="wrap">
			
			<h1><?php _e( 'Knowledgebase Display Settings', 'documentate' )?></h1>
	
			<?php settings_errors( 'documentate_settings' );            ?>
		
			<div class="documentate_admin_settings documentate_admin_left_content">
					
				<form method="post" action="options.php">
			
				<?php settings_fields( 'documentate_settings' ); ?>
			
				<h3 class="title"><?php _e( 'Settings', 'documentate' ); ?></h3>
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table knowedgebase-settings">
					<tr>
						<th><label for="documentate_archive_page_id"><?php _e( 'Knowledgebase Archive', 'documentate' ); ?></label></th>
						<td colspan="3">
							<?php 
							$dropdown_args = array( 'show_option_none' => __( '--Select the Knowledgebase Archive--' ), 
													'name'=>'documentate_settings[archive_page_id]',
													'id' => 'documentate_archive_page_id',
													'selected' => documentate_get_option( 'archive_page_id' ) );
							wp_dropdown_pages( $dropdown_args );
							?> 
						<p class="description">
							<?php printf( __( 'The base page can also be used in your <a href="%s">Knowledgebase permalinks</a>.', 'documentate' ), '<strong>', '</strong>', admin_url( 'options-permalink.php' ) ); ?>
						</p>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Document Archive Display Mode', 'documentate' ); ?></th>
						<td>
							<fieldset>
								<?php $archive_display = documentate_get_option( 'archive_display' ); ?>
								<label><input type="radio" class="toggle-input" name="documentate_settings[archive_display]" value="subcategories" <?php checked( $archive_display, 'subcategories' ); ?>><?php _e( 'Categories', 'documentate' ); ?></label>
								<label><input type="radio" class="toggle-input" name="documentate_settings[archive_display]" value="documents" <?php checked( $archive_display, 'documents' ); ?>><?php _e( 'Documents', 'documentate' ); ?></label>
								<label><input type="radio" class="toggle-input" name="documentate_settings[archive_display]" value="both" <?php checked( $archive_display, 'both' ); ?>><?php _e( 'Both', 'documentate' ); ?></label>
							</fieldset>
							<p class="description">
								<?php printf( __( 'Choose whether to display categories or documents on document archive.', 'documentate' ), '<strong>', '</strong>', admin_url( 'options-permalink.php' ) ); ?>
							</p>
						</td>
					</tr>					
					<tr>
						<th><?php _e( 'Category Archive Display Mode', 'documentate' ); ?></th>
						<td>
							<fieldset>
								<?php $category_display = documentate_get_option( 'category_display' ); ?>
								<label><input type="radio" class="toggle-input" name="documentate_settings[category_display]" value="subcategories" <?php checked( $category_display, 'subcategories' ); ?>><?php _e( 'Categories', 'documentate' ); ?></label>
								<label><input type="radio" class="toggle-input" name="documentate_settings[category_display]" value="documents" <?php checked( $category_display, 'documents' ); ?>><?php _e( 'Documents', 'documentate' ); ?></label>
								<label><input type="radio" class="toggle-input" name="documentate_settings[category_display]" value="both" <?php checked( $category_display, 'both' ); ?>><?php _e( 'Both', 'documentate' ); ?></label>
							</fieldset>
							<p class="description">
								<?php printf( __( 'Choose whether to display categories or documents on category archives. Can be overridden for each individual document category.', 'documentate' ), '<strong>', '</strong>', admin_url( 'options-permalink.php' ) ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th><label for="documentate_docu_qty"><?php _e( 'Number of documents to show per category', 'documentate' ); ?></label></th>
						<td colspan="3">
							<input type="text" name="documentate_settings[docu_qty]" id="documentate_docu_qty" value="<?php echo esc_attr( documentate_get_option( 'docu_qty' ) ); ?>">
							<p class="description">
								<?php _e( 'If showing subcategories, the number of documents to list per category.', 'documentate' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Display search field', 'documentate' ); ?></th>
						<td>
							<fieldset>
								<?php $search_setting = documentate_get_option( 'search_setting' ); ?>
								<label><input type="radio" name="documentate_settings[search_setting]" id="documentate_search_setting" value="show" <?php checked( $search_setting, 'show' ); ?>><?php _e( 'Show', 'documentate' ); ?></label>
								<label><input type="radio" name="documentate_settings[search_setting]" id="documentate_search_setting" value="hide" <?php checked( $search_setting, 'hide' ); ?>><?php _e( 'Hide', 'documentate' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Display breadcrumbs', 'documentate' ); ?></th>
						<td>
							<fieldset>
								<?php $breadcrumbs_setting = documentate_get_option( 'breadcrumbs_setting' ); ?>
								<label><input type="radio" name="documentate_settings[breadcrumbs_setting]" value="show" <?php checked( $breadcrumbs_setting, 'show' ); ?>><?php _e( 'Show', 'documentate' ); ?></label>
								<label><input type="radio" name="documentate_settings[breadcrumbs_setting]" value="hide" <?php checked( $breadcrumbs_setting, 'hide' ); ?>><?php _e( 'Hide', 'documentate' ); ?></label>
							</fieldset>
						</td>
						
					</tr>
					<tr>
						<th><?php _e( 'Uninstall Mode', 'documentate' ); ?></th>
						<td>
							<fieldset>
								<?php $uninstall_mode = documentate_get_option( 'uninstall_mode' ); ?>
								<label><input type="radio" name="documentate_settings[uninstall_mode]" id="documentate_uninstall_nuclear" value="nuclear" <?php checked( $uninstall_mode, 'nuclear' ); ?>><?php _e( 'Everything', 'documentate' ); ?></label>
								<label><input type="radio" name="documentate_settings[uninstall_mode]" id="documentate_uninstall_settings" value="settings" <?php checked( $uninstall_mode, 'settings' ); ?>><?php _e( 'Settings Only', 'documentate' ); ?></label>
								<label><input type="radio" name="documentate_settings[uninstall_mode]" id="documentate_uninstall_none" value="none" <?php checked( $uninstall_mode, 'none' ); ?>><?php _e( 'Nothing', 'documentate' ); ?></label>
							</fieldset>
							<p class="description">
								<?php printf( __( '%sCaution!%s Everything means everything. All documents and settings will be deleted from the database when the plugin is uninstalled.', 'documentate' ), '<strong>', '</strong>' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" value="<?php _e( 'Save Changes', 'documentate' ); ?>" name="submit" id="submit" class="button button-primary">
						</td>
					</tr>
					<?php do_action( 'documentate_plugin_settings' ); ?>
				</table>
				</form>
			</div>
			
			<?php //include_once( 'html-settings-sidebar.php' ); ?>
		
	</div>
</div>