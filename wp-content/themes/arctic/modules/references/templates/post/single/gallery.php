<?php

/**
 * Post Single Gallery
 */

$images = get_post_meta( get_the_ID(), 'reference_images' );

if ( !empty( $images ) ) { ?>

	<section class="f-section f-section--gallery f-section--reference-gallery">
		<div class="f-section__container a-container">

			<header class="f-section__header screen-reader-text">
				<h2><?php echo esc_html__( 'Gallery', 'baspa' ); ?></h2>
			</header>

			<?php get_template_part( 'templates/image/gallery', '', array(
				'meta_key' => 'reference_images',
			) ); ?>
		</div>
	</section>

<?php }
