<?php
/**
 * Template part for displaying entry category.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

?>

<div class="post-category">

	<?php
	do_action( 'dekanpro_before_post_category' );

	if ( is_singular() ) {
		dekanpro_entry_meta_category( ' ', false );
	} else {
		if ( 'blog-horizontal' === dekanpro_get_article_feed_layout() || 'blog-layout-2' === dekanpro_get_article_feed_layout() ) {
			dekanpro_entry_meta_category( ' ', false, 3 );
		} else {
			dekanpro_entry_meta_category( ', ', false, 3 );
		}
	}

	do_action( 'dekanpro_after_post_category' );
	?>

</div>
