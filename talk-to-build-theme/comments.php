<?php
/**
 * Comments template.
 *
 * Required by WordPress — without this file WP generates a notice.
 * The Canvas template never renders comments, but standard pages may.
 *
 * @package Talk-to-Build
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			printf(
				/* translators: %s: number of comments */
				esc_html( _n( '%s comment', '%s comments', $comment_count, 'talk-to-build' ) ),
				esc_html( number_format_i18n( $comment_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
			) );
			?>
		</ol>

		<?php the_comments_pagination(); ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'talk-to-build' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div>
