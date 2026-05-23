<?php

/**
 * Listing
 */

$position = get_post_meta( get_the_ID(), 'member_position', true );
$scope    = get_post_meta( get_the_ID(), 'member_scope', true );
$email    = get_post_meta( get_the_ID(), 'member_email', true );
$phone    = get_post_meta( get_the_ID(), 'member_phone', true );

// Class
$post_class = array( 'f-listing--member', 'f-listing' );

if ( !has_post_thumbnail() ) {
	$post_class[] = 'f-listing--no-image';
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<?php
	get_template_part( 'modules/members/templates/post/listing/image' );
	?>

	<div class="f-listing__container a-stack a-gap--s">
		<header class="f-listing__header a-stack a-gap--0">
			<h3><?php the_title(); ?></h3>
			<?php if ( !empty( $position ) ) { ?>
				<small class="f-listing__position"><?php echo wp_kses_post( $position ); ?></small>
			<?php } ?>
		</header>

		<?php if ( !empty( $scope ) ) { ?>
			<div class="f-listing__scope"><?php echo wp_kses_post( $scope ); ?></div>
		<?php } ?>
	</div>

</article>
