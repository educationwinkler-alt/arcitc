<?php

/**
 * Post Single Meta
 */

global $post;
?>

<ul class="f-heading__metas f-metas f-metas--align-center">
	<li class="f-author f-meta">
		<div class="a-flex a-gap--xs a-flex--align-center">
			<?php if ( function_exists( 'forqy_author_get_avatar' ) ) { ?>
				<div class="a-flex__item">
					<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>" tabindex="-1">
						<?php forqy_author_get_avatar( get_the_author_meta( 'ID', $post->post_author ) ); ?>
					</a>
				</div>
			<?php } ?>
			<div class="a-flex__item--auto">
				<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>" class="f-author__name">
					<?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?>
				</a>
				<div class="f-author__position">
					<?php if ( !empty( get_the_author_meta( 'position', $post->post_author ) ) ) {
						echo esc_html( get_the_author_meta( 'position', $post->post_author ) );
					} ?>
				</div>
			</div>
		</div>
	</li>
	<li class="f-meta">
		<time datetime="<?php echo get_the_date( DATE_W3C ); ?>">
			<?php echo get_the_date(); ?>
		</time>
	</li>
	<?php if ( is_user_logged_in() && current_user_can( 'edit_post', get_the_ID() ) ) { ?>
		<li class="f-meta"><?php edit_post_link( __( 'Edit', 'baspa' ) ); ?></li>
	<?php } ?>
</ul>
