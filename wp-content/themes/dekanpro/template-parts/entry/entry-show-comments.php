<?php
/**
 * Template part for displaying ”Show Comments” button.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

// Do not show if the post is password protected.
if ( post_password_required() ) {
	return;
}

$dekanpro_comment_count = get_comments_number();
$dekanpro_comment_title = esc_html__( 'Leave a Comment', 'dekanpro' );

if ( $dekanpro_comment_count > 0 ) {
	/* translators: %s is comment count */
	$dekanpro_comment_title = esc_html( sprintf( _n( 'Show %s Comment', 'Show %s Comments', $dekanpro_comment_count, 'dekanpro' ), $dekanpro_comment_count ) );
}

?>
<a href="#" id="dekanpro-comments-toggle" class="dekanpro-btn btn-large btn-fw btn-left-icon">
	<?php echo dekanpro()->icons->get_svg( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<span><?php echo $dekanpro_comment_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
</a>
