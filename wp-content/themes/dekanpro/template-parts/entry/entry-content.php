<?php
/**
 * Template part for displaying entry content.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'dekanpro_before_entry_content' ); ?>
<div class="entry-content dekanpro-entry"<?php dekanpro_schema_markup( 'text' ); ?>>
	<?php the_content(); ?>
</div>

<?php dekanpro_link_pages(); ?>

<?php do_action( 'dekanpro_after_entry_content' ); ?>
