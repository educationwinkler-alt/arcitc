<?php

/**
 * Comments
 *
 * @package     forqys/function
 * @since       1.0.0
 */

if ( !function_exists( 'forqy_comments_list' ) ) {

	/**
	 * Comments List
	 *
	 * @param $comment
	 * @param $args
	 * @param $depth
	 */
	function forqy_comments_list( $comment, $args, $depth ): void {

		// Author
		$comment_author_id        = $comment->user_id;
		$comment_author_avatar_id = get_user_meta( $comment_author_id, 'avatar', true );

		// Avatar
		if ( !empty( $comment_author_avatar_id ) ) {
			$avatar_image = wp_get_attachment_image_src( $comment_author_avatar_id, array( 240, 240 ) );
			$avatar       = '<img src="' . esc_url( $avatar_image[ 0 ] ) . '" alt="' . esc_attr( $comment->comment_author ) . '" width="' . esc_attr( $avatar_image[ 1 ] ) . '" height="' . esc_attr( $avatar_image[ 2 ] ) . '" fetchpriority="low" decoding="async">';
		} else {
			$avatar = get_avatar( $comment, 240, 'avatar_default', esc_attr( $comment->comment_author ), array( 'fetchpriority' => 'low' ) );
		} ?>

	<article id="comment-<?php comment_ID(); ?>" <?php comment_class( array( 'f-comment' ) ); ?>
	         itemscope itemtype="https://schema.org/Comment">

		<div class="f-comment__container a-stack a-gap--xxs">

			<header class="f-comment__header">
				<?php if ( !empty( $avatar ) ) { ?>
					<div class="f-comment__avatar f-avatar">
						<?php echo wp_kses_post( $avatar ); ?>
					</div>
				<?php } ?>

				<h3 class="f-comment__author" itemprop="name">
					<?php echo get_comment_author_link(); ?>
				</h3>

				<time class="f-comment__date" datetime="<?php comment_time( 'c' ); ?>">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
						<?php echo get_comment_date() . ' &mdash; ' . get_comment_time(); ?>
					</a>
				</time>
			</header>

			<div class="f-comment__body">
				<?php if ( $comment->comment_approved === '0' ) { ?>
					<div class="f-comment__alert f-alert a-alert" role="alert">
						<?php
						if ( !empty( apply_filters( 'forqy_translation', 'comments_comment_awaiting_moderation' ) ) ) {
							echo apply_filters( 'forqy_translation', 'comments_comment_awaiting_moderation' );
						} else {
							echo __( 'Your comment is awaiting moderation.' );
						} ?>
					</div>
				<?php } ?>

				<div class="f-comment__content f-content a-content" itemprop="text">
					<?php comment_text(); ?>
				</div>
			</div>

			<div class="f-comment__toolbar">
				<?php
				/**
				 * Reply Link
				 */
				$comment_reply_link_args = array(
					'depth'     => $depth,
					'max_depth' => $args[ 'max_depth' ],
				);

				if ( !empty( apply_filters( 'forqy_translation', 'comments_comment_reply' ) ) ) {
					$comment_reply_link_args[ 'reply_text' ] = apply_filters( 'forqy_translation', 'comments_comment_reply' );
				}
				if ( !empty( apply_filters( 'forqy_translation', 'comments_comment_reply_login' ) ) ) {
					$comment_reply_link_args[ 'login_text' ] = apply_filters( 'forqy_translation', 'comments_comment_reply_login' );
				}
				comment_reply_link( $comment_reply_link_args );

				/**
				 * Edit Link
				 */
				if ( !empty( apply_filters( 'forqy_translation', 'comments_comment_edit' ) ) ) {
					edit_comment_link( apply_filters( 'forqy_translation', 'comments_comment_edit' ) );
				} else {
					edit_comment_link();
				} ?>
			</div>

		</div>

		<?php
		// Don't close <article>, will be closed using next function
	}

}

if ( !function_exists( 'forqy_comments_list_end' ) ) {

	/**
	 * Comments List - End
	 */
	function forqy_comments_list_end(): void {
		echo '</article>';
	}

}
