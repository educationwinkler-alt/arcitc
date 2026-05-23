<?php

/**
 * Contact Section
 */

?>

<section id="<?php echo sanitize_title( esc_attr_x( 'contact', 'anchor', 'baspa' ) ); ?>"
         class="f-section f-section--accent f-section--contact">

	<div class="f-section__container a-container">

		<div class="a-flex a-gap--m a-gap--xl:m">
			<div class="a-flex__item--100 a-flex__item--50:m">

				<header class="f-section__header f-section__header--center">
					<h2><?php if ( !empty( get_option( 'baspa_contacts_title' ) ) ) {
							echo wp_kses_post( get_option( 'baspa_contacts_title' ) );
						} else {
							echo wp_kses_post( __( 'Kontaktujte nás', 'baspa' ) );
						} ?></h2>
					<?php if ( !empty( get_option( 'baspa_contacts_subtitle' ) ) ) { ?>
						<div class="f-section__subtitle">
							<?php echo wp_kses_post( wpautop( get_option( 'baspa_contacts_subtitle' ) ) ); ?>
						</div>
					<?php } ?>
				</header>

				<div class="f-section__content f-content">
					<?php block_template_part( 'contacts' ); ?>
				</div>

			</div>
			<div class="a-flex__item--100 a-flex__item--50:m">

				<div class="f-section__form">
					<?php get_template_part( 'modules/contacts/templates/form', 'contact', array(
						'type'   => 'thin',
						'header' => true,
					) ); ?>
				</div>

			</div>
		</div>

	</div>

</section>
