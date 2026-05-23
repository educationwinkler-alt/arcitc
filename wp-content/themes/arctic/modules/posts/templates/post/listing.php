<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-listing', 'f-listing--post' );
if ( isset( $args[ 'post_number' ] ) ) {
	$post_class[] = 'f-listing--' . esc_attr( $args[ 'post_number' ] );

	if ( $args[ 'post_number' ] == 1 ) {
		$post_class[] = 'f-listing--first';
	}
}
if ( !has_post_thumbnail() ) {
	$post_class[] = 'f-listing--no-image';
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="f-listing__image--container">
		<?php
		if ( !( isset( $args[ 'image' ] ) && !$args[ 'image' ] ) ) {
			get_template_part( 'templates/image/listing' );
		}
		get_template_part( 'modules/posts/templates/post/common/categories', '', array(
			'questions' => true,
		) );
		?>
	</div>

	<div class="f-listing__container a-stack a-gap--xs">

		<header class="f-listing__header a-stack a-gap--xs">
			<?php
			get_template_part( 'templates/listing/header' );
			?>
		</header>

		<?php if ( has_excerpt() ) { ?>
			<div class="f-listing__excerpt">
				<?php echo wp_trim_words( get_the_excerpt(), 20, __( ' ...', 'baspa' ) ); ?>
			</div>
		<?php }

		get_template_part( 'modules/posts/templates/post/listing/footer' );
		?>
	</div>

</article>
