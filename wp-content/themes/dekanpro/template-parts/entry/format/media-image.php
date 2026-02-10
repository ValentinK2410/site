<?php
/**
 * Template part for displaying post format image entry.
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

$dekanpro_media = dekanpro_get_post_media( 'image' );

if ( ! $dekanpro_media || post_password_required() ) {
	return;
}

?>

<div class="post-thumb entry-media thumbnail">

	<?php
	if ( ! is_single( get_the_ID() ) ) {
		$dekanpro_media = sprintf(
			'<a href="%1$s" class="entry-image-link">%2$s</a>',
			esc_url( dekanpro_entry_get_permalink() ),
			$dekanpro_media
		);
	}

	echo $dekanpro_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</div>
