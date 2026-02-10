<?php
/**
 * Template part for displaying entry footer.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php do_action( 'dekanpro_before_entry_footer' ); ?>
<footer class="entry-footer">
	<?php

	// Allow text to be filtered.
	$dekanpro_read_more_text = dekanpro_option( 'blog_read_more' );

	?>
	<a href="<?php echo esc_url( dekanpro_entry_get_permalink() ); ?>" class="dekanpro-btn btn-text-1"><span><?php echo esc_html( $dekanpro_read_more_text ); ?></span></a>
</footer>
<?php do_action( 'dekanpro_after_entry_footer' ); ?>
