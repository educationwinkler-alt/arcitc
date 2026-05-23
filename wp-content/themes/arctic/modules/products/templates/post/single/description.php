<?php

/**
 * Single Description
 */

global $post;

if ( get_the_content( get_the_ID() ) ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'description', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--single f-section--description js-links__section">

		<div class="f-section__container a-container">
			<header class="f-section__header<?php if ( $post->post_parent == 0 ) { ?> screen-reader-text<?php } ?>">
				<h2><?php if ( baspa_products_query_product_has_parameters( get_the_ID() ) ) {
						echo esc_html__( 'Parameters and Description', 'baspa' );
					} else {
						echo esc_html__( 'Description', 'baspa' );
					} ?></h2>
			</header>

			<?php
			get_template_part( 'modules/products/templates/post/single/parameters' );
			get_template_part( 'modules/products/templates/post/single/configurations' );
			get_template_part( 'templates/content' );
			?>
		</div>

	</section>

<?php }
