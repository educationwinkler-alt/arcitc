<?php

/**
 * Card Listing
 */

$position = get_post_meta( get_the_ID(), 'member_position', true );
$scope    = get_post_meta( get_the_ID(), 'member_scope', true );
$email    = get_post_meta( get_the_ID(), 'member_email', true );
$phone    = get_post_meta( get_the_ID(), 'member_phone', true );

// Class
$post_class = array( 'f-listing--member', 'f-listing--member-card', 'f-listing' );

if ( !has_post_thumbnail() ) {
	$post_class[] = 'f-listing--no-image';
} ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="f-listing__container a-stack a-gap--xs">
		<div class="a-flex a-flex--align-center">
			<div class="a-flex__item">

				<?php get_template_part( 'modules/members/templates/post/listing/image' ); ?>

			</div>
			<div class="a-flex__item--auto">

				<header class="f-listing__header a-stack a-gap--0">
					<h3><?php the_title(); ?></h3>
					<?php if ( !empty( $position ) ) { ?>
						<small class="f-listing__position"><?php echo wp_kses_post( $position ); ?></small>
					<?php } ?>
				</header>

			</div>
		</div>

		<?php if ( !empty( $scope ) ) { ?>
			<div class="f-listing__scope"><?php echo wp_kses_post( $scope ); ?></div>
		<?php } ?>

		<?php if ( !empty( $email ) || !empty( $phone ) ) { ?>
			<div class="f-listing__contacts a-stack a-gap--0">
				<?php if ( !empty( $email ) ) { ?>
					<a href="mailto:<?php echo antispambot( esc_attr( $email ) ); ?>"
					   class="f-listing__email"><?php echo antispambot( esc_html( $email ) ); ?></a>
				<?php }
				if ( !empty( $phone ) ) { ?>
					<a href="tel:<?php echo esc_attr( str_replace( ' ', '', $phone ) ); ?>"
					   class="f-listing__phone"><?php echo esc_html( $phone ); ?></a>
				<?php } ?>
			</div>
		<?php } ?>
	</div>

</article>
