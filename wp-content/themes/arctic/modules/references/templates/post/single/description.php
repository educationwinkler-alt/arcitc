<?php

/**
 * Post Single Description
 */

$description = get_post_meta( get_the_ID(), 'reference_description', true );

if ( !empty( $description ) ) { ?>

	<section class="f-section f-section--description">
		<div class="f-section__container a-container">
			<div class="a-container--50 a-container--align-start">
				<header class="f-section__header">
					<h2><?php echo esc_html__( 'Project Description', 'baspa' ); ?></h2>
				</header>

				<div class="f-content a-content">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			</div>
		</div>
	</section>

<?php }