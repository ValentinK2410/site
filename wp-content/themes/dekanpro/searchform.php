<?php
/**
 * The template for displaying search form.
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

$dekanpro_aria_label = ! empty( $args['aria_label'] ) ? 'aria-label="' . esc_attr( $args['aria_label'] ) . '"' : 'aria-label="' . esc_attr__( 'Search for:', 'dekanpro' ) . '"';

// Support for custom search post type.
$dekanpro_post_type = apply_filters( 'dekanpro_search_post_type', 'all' );
$dekanpro_post_type = 'all' !== $dekanpro_post_type ? '<input type="hidden" name="post_type" value="' . esc_attr( $dekanpro_post_type ) . '" />' : '';
?>

<form role="search" <?php echo $dekanpro_aria_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?> method="get" class="dekanpro-search-form search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div>
		<input type="search" class="dekanpro-input-search search-field" aria-label="<?php esc_attr_e( 'Enter search keywords', 'dekanpro' ); ?>" placeholder="<?php esc_attr_e( 'Search', 'dekanpro' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
		<?php echo $dekanpro_post_type; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		
		<?php if( !isset( $args['icon'] ) ): ?>
		<button role="button" type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Search', 'dekanpro' ); ?>">
			<?php echo dekanpro()->icons->get_svg( 'search', array( 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<?php else:
		dekanpro_animated_arrow( 'right', 'submit', true );
		?>
		<button type="button" class="dekanpro-search-close" aria-hidden="true" role="button">
			<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M6.852 7.649L.399 1.195 1.445.149l6.454 6.453L14.352.149l1.047 1.046-6.454 6.454 6.454 6.453-1.047 1.047-6.453-6.454-6.454 6.454-1.046-1.047z" fill="currentColor" fill-rule="evenodd"></path></svg>
		</button>
		<?php endif; ?>
	</div>
</form>