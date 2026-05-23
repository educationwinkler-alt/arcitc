<?php

/**
 * Contact Section
 */

?>

<section id="<?php echo sanitize_title( esc_attr_x( 'service-form', 'anchor', 'baspa' ) ); ?>"
         class="f-section f-section--service js-links__section">

	<div class="f-section__container a-container">

		<header class="f-section__header a-stack a-gap--s">
			<h2><?php if ( !empty( get_option( 'baspa_service_title' ) ) ) {
					echo wp_kses_post( get_option( 'baspa_service_title' ) );
				} else {
					echo wp_kses_post( __( 'Servisní formulář', 'baspa' ) );
				} ?></h2>
			<?php if ( !empty( get_option( 'baspa_service_subtitle' ) ) ) { ?>
				<div class="f-section__subtitle">
					<?php echo wp_kses_post( wpautop( get_option( 'baspa_service_subtitle' ) ) ); ?>
				</div>
			<?php } ?>
		</header>

		<div class="f-section__form">
			<?php get_template_part( 'modules/contacts/templates/form', 'service', array(
				'type'   => 'thin',
				'header' => false,
			) ); ?>
		</div>

	</div>

</section>
