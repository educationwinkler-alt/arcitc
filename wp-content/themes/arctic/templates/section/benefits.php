<?php

/**
 * Arctic Benefits
 */

$benefits = array(
	array(
		'title' => __( 'Montáž', 'baspa' ),
		'text'  => __( 'Odborně na klíč', 'baspa' ),
		'icon'  => 'box',
	),
	array(
		'title' => __( 'Podpora', 'baspa' ),
		'text'  => __( 'Se vším vám poradíme', 'baspa' ),
		'icon'  => 'support',
	),
	array(
		'title' => __( 'Servis', 'baspa' ),
		'text'  => __( 'Jsme tu pro vás 24/7', 'baspa' ),
		'icon'  => 'service',
	),
);
?>

<section class="f-section f-section--arctic-benefits">
	<div class="f-section__container a-container">
		<div class="f-arctic-benefits">
			<?php foreach ( $benefits as $benefit ) { ?>
				<article class="f-arctic-benefit">
					<span class="f-arctic-benefit__icon f-arctic-benefit__icon--<?php echo esc_attr( $benefit['icon'] ); ?>" aria-hidden="true"></span>
					<div>
						<h3><?php echo esc_html( $benefit['title'] ); ?></h3>
						<p><?php echo esc_html( $benefit['text'] ); ?></p>
					</div>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
