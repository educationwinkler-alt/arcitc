<?php

/**
 * Author
 *
 * @package     forqys/function
 * @since       1.0.0
 */

if ( !function_exists( 'forqy_author_get_avatar' ) ) {

	/**
	 * Get Author Avatar
	 *
	 * @param int|null $author_id
	 *
	 * @return void
	 */
	function forqy_author_get_avatar( int $author_id = null ): void {

		if ( $author_id ) {
			$author       = get_userdata( $author_id );
			$avatar       = get_user_meta( $author_id, 'avatar', true );
			$avatar_image = wp_get_attachment_image_src( $avatar, get_template() . '-avatar' );

			if ( !empty( $avatar_image ) ) { ?>
				<img class="f-avatar f-avatar--author"
				     src="<?php echo esc_url( $avatar_image[ 0 ] ); ?>"
				     width="<?php echo esc_attr( $avatar_image[ 1 ] ); ?>"
				     height="<?php echo esc_attr( $avatar_image[ 2 ] ); ?>"
				     alt="<?php echo esc_attr( $author->display_name ); ?>"
				     fetchpriority="low"
				     decoding="async">
			<?php } else { ?>
				<img class="f-avatar f-avatar--author f-avatar--default"
				     src="<?php echo esc_url( get_theme_file_uri( 'images/avatar.png' ) ); ?>"
				     width="240"
				     height="240"
				     alt="<?php echo esc_attr( $author->display_name ); ?>"
				     fetchpriority="low"
				     decoding="async">
			<?php }
		}

	}

}
