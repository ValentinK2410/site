<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

/*
 * Return if comments are not meant to be displayed.
 */
if ( ! dekanpro_comments_displayed() ) {
	return;
}

?>
<?php do_action( 'dekanpro_before_comments' ); ?>
<section id="comments" class="comments-area">

	<div class="comments-title-wrapper center-text">
		<h3 class="comments-title">
			<?php

			// Get comments number.
			$dekanpro_comments_count = get_comments_number();

			if ( 0 === intval( $dekanpro_comments_count ) ) {
				$dekanpro_comments_title = esc_html__( 'Comments', 'dekanpro' );
			} else {
				/* translators: %s Comment number */
				$dekanpro_comments_title = sprintf( _n( '%s Comment', '%s Comments', $dekanpro_comments_count, 'dekanpro' ), number_format_i18n( $dekanpro_comments_count ) );
			}

			// Apply filters to the comments count.
			$dekanpro_comments_title = apply_filters( 'dekanpro_comments_count', $dekanpro_comments_title );

			echo wp_kses( $dekanpro_comments_title, dekanpro_get_allowed_html_tags() );
			?>
		</h3><!-- END .comments-title -->

		<?php
		if ( ! have_comments() ) {
			$dekanpro_no_comments_title = apply_filters( 'dekanpro_no_comments_text', esc_html__( 'No comments yet. Why don&rsquo;t you start the discussion?', 'dekanpro' ) );
			?>
			<p class="no-comments"><?php echo esc_html( $dekanpro_no_comments_title ); ?></p>
		<?php } ?>
	</div>

	<ol class="comment-list">
		<?php

		// List comments.
		wp_list_comments(
			array(
				'callback'    => 'dekanpro_comment',
				'avatar_size' => apply_filters( 'dekanpro_comment_avatar_size', 50 ),
				'reply_text'  => __( 'Reply', 'dekanpro' ),
			)
		);
		?>
	</ol>

	<?php
	// If comments are closed and there are comments, let's leave a note.
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="comments-closed center-text"><?php esc_html_e( 'Comments are closed', 'dekanpro' ); ?></p>
	<?php endif; ?>

	<?php
	the_comments_pagination(
		array(
			'prev_text' => '<span class="screen-reader-text">' . __( 'Previous', 'dekanpro' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'dekanpro' ) . '</span>',
		)
	);
	?>

	<?php
	comment_form(
		array(
			/* translators: %1$s opening anchor tag, %2$s closing anchor tag */
			'must_log_in'   => '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a comment.', 'dekanpro' ), '<a href="' . wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) . '">', '</a>' ) . '</p>', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			'logged_in_as'  => '<p class="logged-in-as">' . esc_html__( 'Logged in as', 'dekanpro' ) . ' <a href="' . esc_url( admin_url( 'profile.php' ) ) . '">' . $user_identity . '</a> <a href="' . wp_logout_url( get_permalink() ) . '" title="' . esc_html__( 'Log out of this account', 'dekanpro' ) . '">' . esc_html__( 'Log out?', 'dekanpro' ) . '</a></p>',
			'title_reply'   => esc_html__( 'Leave a Reply', 'dekanpro' ),
			'label_submit'  => esc_html__( 'Post Comment', 'dekanpro' ),
			'class_submit'  => 'dekanpro-btn primary-button',
			'comment_field' => '<p class="comment-textarea"><textarea name="comment" id="comment" cols="44" rows="8" class="textarea-comment" placeholder="' . esc_html__( 'Write a comment&hellip;', 'dekanpro' ) . '" required="required"></textarea></p>',
			'id_submit'     => 'comment-submit',
		)
	);
	?>

</section><!-- #comments -->
<?php do_action( 'dekanpro_after_comments' ); ?>
