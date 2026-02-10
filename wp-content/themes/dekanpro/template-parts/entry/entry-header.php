<?php
/**
 * Template part for displaying entry header.
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

<?php do_action( 'dekanpro_before_entry_header' ); ?>
<header class="entry-header">

	<?php
	$dekanpro_tag = is_single( get_the_ID() ) && ! dekanpro_page_header_has_title() ? 'h1' : 'h4';
	$dekanpro_tag = apply_filters( 'dekanpro_entry_header_tag', $dekanpro_tag );

	$dekanpro_title_string = '%2$s%1$s';

	if ( 'link' === get_post_format() ) {
		$dekanpro_title_string = '<a href="%3$s" title="%3$s" rel="bookmark">%2$s%1$s</a>';
	} elseif ( ! is_single( get_the_ID() ) ) {
		$dekanpro_title_string = '<a href="%3$s" title="%4$s" rel="bookmark">%2$s%1$s</a>';
	}

	$dekanpro_title_icon = apply_filters( 'dekanpro_post_title_icon', '' );
	$dekanpro_title_icon = dekanpro()->icons->get_svg( $dekanpro_title_icon );
	?>

	<<?php echo tag_escape( $dekanpro_tag ); ?> class="entry-title"<?php dekanpro_schema_markup( 'headline' ); ?>>
		<?php
		echo sprintf(
			wp_kses_post( $dekanpro_title_string ),
			wp_kses_post( get_the_title() ),
			wp_kses_post( (string) $dekanpro_title_icon ),
			esc_url( dekanpro_entry_get_permalink() ),
			the_title_attribute( array( 'echo' => false ) )
		);
		?>
	</<?php echo tag_escape( $dekanpro_tag ); ?>>

</header>
<?php do_action( 'dekanpro_after_entry_header' ); ?>
