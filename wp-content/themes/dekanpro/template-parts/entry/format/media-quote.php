<?php
/**
 * Template part for displaying quote format entry.
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

if ( post_password_required() ) {
	return;
}

$dekanpro_quote_content = apply_filters( 'dekanpro_post_format_quote_content', get_the_content() );
$dekanpro_quote_author  = apply_filters( 'dekanpro_post_format_quote_author', get_the_title() );
$dekanpro_quote_bg      = has_post_thumbnail() ? ' style="background-image: url(\'' . esc_url( get_the_post_thumbnail_url() ) . '\')"' : '';
?>

<div class="dekanpro-blog-entry-content">
	<div class="entry-content dekanpro-entry"<?php dekanpro_schema_markup( 'text' ); ?>>

		<?php if ( ! is_single() ) { ?>
			<a href="<?php the_permalink(); ?>" class="quote-link" aria-label="<?php esc_attr_e( 'Read more', 'dekanpro' ); ?>"></a>
		<?php } ?>

			<div class="quote-post-bg"<?php echo $dekanpro_quote_bg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></div>

			<div class="quote-inner">

				<?php echo dekanpro()->icons->get_svg( 'quote', array( 'class' => 'icon-quote' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<h3><?php echo wp_kses( $dekanpro_quote_content, dekanpro_get_allowed_html_tags() ); ?></h3>
				<div class="author"><?php echo wp_kses( $dekanpro_quote_author, dekanpro_get_allowed_html_tags() ); ?></div>

			</div><!-- END .quote-inner -->

	</div>
</div><!-- END .dekanpro-blog-entry-content -->
