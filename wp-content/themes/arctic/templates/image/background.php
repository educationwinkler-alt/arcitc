<?php

/**
 * Background Image
 */

$page_id = is_home() && !is_front_page() ? get_option( 'page_for_posts' ) : get_the_ID();

// Media
$media_class = array( 'f-background', 'f-background__image' );

if ( is_tax() ) {
	$term_image_id      = get_term_meta( get_queried_object_id(), 'category_heading_image', true );
	$term_image_id      = !empty( $term_image_id ) ? $term_image_id : get_term_meta( get_queried_object_id(), 'category_image', true );
	$term_image         = wp_get_attachment_image_src( $term_image_id, get_template() . '-huge' );
	$term_image_caption = wp_get_attachment_caption( $term_image_id );

	if ( !empty( $term_image ) ) {
		/**
		 * Term Image
		 */
		?>

		<figure <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $media_class ); ?>>
			<img src="<?php echo esc_url( $term_image[ 0 ] ); ?>"
			     alt="<?php echo esc_attr( $term_image_caption ); ?>"
			     width="<?php echo esc_attr( $term_image[ 1 ] ); ?>"
			     height="<?php echo esc_attr( $term_image[ 2 ] ); ?>"
			     fetchpriority="high"
			     decoding="async">

			<?php if ( !empty( $term_image_caption ) ) { ?>
				<figcaption class="f-background__caption">
					<?php echo esc_html( $term_image_caption ); ?>
				</figcaption>
			<?php } ?>
		</figure>

	<?php }

} else {
	if ( has_post_thumbnail( $page_id ) ) {

		/**
		 * Thumb
		 */
		$media_class[] = 'f-background__image--thumb';
		if ( is_home() && !is_front_page() ) {
			$media_class[] = 'f-background__image--index';
		} ?>

		<figure <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $media_class ); ?>>
			<?php
			add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
			echo get_the_post_thumbnail( $page_id, get_template() . '-huge', array(
				'data-lazy'     => false,
				'fetchpriority' => 'high',
			) );
			remove_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
			?>

			<?php if ( get_the_post_thumbnail_caption() ) { ?>
				<figcaption class="f-background__caption">
					<?php the_post_thumbnail_caption(); ?>
				</figcaption>
			<?php } ?>
		</figure>

	<?php } else if ( has_header_image() ) {

		/**
		 * Header
		 */
		$media_class[] = 'f-background__image--header'; ?>

		<figure <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $media_class ); ?>>
			<?php
			add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
			the_header_image_tag();
			remove_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
			?>
		</figure>

	<?php }
}
