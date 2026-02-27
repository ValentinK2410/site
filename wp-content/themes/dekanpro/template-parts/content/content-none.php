<?php
/**
 * Template part for displaying not found posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

?>

<section class="no-results not-found">

	<div class="page-content dekanpro-entry">

		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'dekanpro' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>
			<p><?php esc_html_e( 'К сожалению, по вашему запросу ничего не найдено. Попробуйте другие ключевые слова.', 'dekanpro' ); ?></p>
			<?php
			get_search_form();

		elseif ( is_category() ) :
			$contrib_page = get_page_by_path( 'dobavit-material' );
			$contrib_url  = $contrib_page ? get_permalink( $contrib_page ) : '';
			?>
			<p><?php esc_html_e( 'В этой рубрике пока нет опубликованных записей.', 'dekanpro' ); ?></p>
			<p class="no-results-hint"><?php esc_html_e( 'Станьте первым автором — добавьте свой материал!', 'dekanpro' ); ?></p>
			<?php if ( $contrib_url ) : ?>
				<p class="no-results-action"><a href="<?php echo esc_url( $contrib_url ); ?>" class="dekanpro-btn primary-button"><?php esc_html_e( 'Добавить материал', 'dekanpro' ); ?></a></p>
			<?php endif; ?>
			<?php

		elseif ( is_tax() ) :
			?>
			<p><?php esc_html_e( 'В этой рубрике пока нет опубликованных записей.', 'dekanpro' ); ?></p>
			<?php

		elseif ( is_tag() ) :
			?>
			<p><?php esc_html_e( 'По этой метке пока нет опубликованных записей.', 'dekanpro' ); ?></p>
			<?php

		else :
			?>
			<p><?php esc_html_e( 'К сожалению, мы не можем найти то, что вы ищете. Попробуйте воспользоваться поиском.', 'dekanpro' ); ?></p>
			<?php
			get_search_form();

		endif;
		?>

	</div><!-- .page-content -->
</section><!-- .no-results -->
