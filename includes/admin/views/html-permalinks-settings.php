<?php
/**
 * Document Admin Permalinks
 *
 * @version     0.1-beta
 * @author      helgatheviking
 * @category    Admin
 * @package     Documentate/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo wpautop( __( 'These settings control the permalinks used for documents. These settings only apply when <strong>not</strong> using "default" permalinks above.', 'documentate' ) );

$document_permalink = documentate_get_option( 'document_base' );
$category_slug = documentate_get_option( 'category_base' );

// Get archive page
$archive_page_id = documentate_get_option( 'archive_page_id' );
	 
$archive_slug      = urldecode( ( $archive_page_id > 0 && get_post( $archive_page_id ) ) ? get_page_uri( $archive_page_id ) : _x( 'knowledgebase', 'default slug', 'documentate' ) );
$default_base   = _x( 'document', 'default slug', 'documentate' );

$structures = array(
	0 => '',
	1 => '/' . trailingslashit( $default_base ),
	2 => '/' . trailingslashit( $archive_slug ),
	3 => '/' . trailingslashit( $archive_slug ) . trailingslashit( '%docu_cat%' )
);

	?>
<table class="form-table">
	<tbody>
		<tr>
			<th><label><input name="documentate_permalinks[document_permalink]" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="kbetog" <?php checked( $structures[0], $document_permalink ); ?> /> <?php _e( 'Default', 'documentate' ); ?></label></th>
			<td><code><?php echo esc_html( home_url() ); ?>/?document=sample-document</code></td>
		</tr>
		<tr>
			<th><label><input name="documentate_permalinks[document_permalink]" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="kbetog" <?php checked( $structures[1], $document_permalink ); ?> /> <?php _e( 'Document', 'documentate' ); ?></label></th>
			<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $default_base ); ?>/sample-document/</code></td>
		</tr>
	<?php if ( $archive_page_id ) : ?>
		<tr>
			<th><label><input name="documentate_permalinks[document_permalink]" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" class="kbetog" <?php checked( $structures[2], $document_permalink ); ?> /> <?php _e( 'Document archive', 'documentate' ); ?></label></th>
			<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $archive_slug ); ?>/sample-document/</code></td>
		</tr>
		<tr>
			<th><label><input name="documentate_permalinks[document_permalink]" type="radio" value="<?php echo esc_attr( $structures[3] ); ?>" class="kbetog" <?php checked( $structures[3], $document_permalink ); ?> /> <?php _e( 'Document archive with category', 'documentate' ); ?></label></th>
			<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $archive_slug );?>/docu_cat/sample-document/</code></td>
		</tr>
	<?php endif; ?>
	<tr>
		<th><label><input name="documentate_permalinks[document_permalink]" id="knowledgebase_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $document_permalink, $structures ), false ); ?> />
			<?php _e( 'Custom Base', 'documentate' ); ?></label></th>
			<td>
				<input name="documentate_permalinks[document_permalink_structure]" id="knowledgebase_permalink_structure" type="text" value="<?php echo esc_attr( $document_permalink ); ?>" class="regular-text code"> <span class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'documentate' ); ?></span>
			</td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
	jQuery( function() {
		jQuery('input.kbetog').change(function() {
			jQuery('#knowledgebase_permalink_structure').val( jQuery( this ).val() );
		});
		jQuery('#knowledgebase_permalink_structure').focus( function(){
			jQuery('#knowledgebase_custom_selection').click();
		} );
	} );
</script>
