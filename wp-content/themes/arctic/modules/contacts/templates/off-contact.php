<?php

/**
 * Contact Off
 */

?>

<aside id="<?php echo sanitize_title( esc_attr_x( 'contact-us', 'anchor', 'baspa' ) ); ?>"
       class="f-off f-off--contact f-off--dialog a-off a-off--dialog js-off"
       data-off="contact"
       data-off-breakpoint="all"
       data-off-relocate="false">

	<div class="f-off__container a-off__container a-off__container--50">

		<button class="f-off__close a-off__close js-off__close"
		        data-off="contact"
		        aria-controls="<?php echo sanitize_title( esc_attr_x( 'contact-us', 'anchor', 'baspa' ) ); ?>">
			<?php if ( function_exists( 'forqy_get_icon' ) ) {
				forqy_get_icon( 'close' );
			} ?>
			<span class="screen-reader-text"><?php echo esc_attr_x( 'Close', 'navigation', 'baspa' ); ?></span>
		</button>

		<div class="f-off__icon f-icon">
			<?php get_template_part( 'images/icon/form/contact' ); ?>
		</div>

		<div class="f-off__scroller a-off__scroller">

			<div class="a-stack">
				<header class="f-off__header f-off__header--center a-stack a-stack--justify-center a-gap--xxs">
					<h2 class="a-stack a-gap--xs">
						<?php if ( !empty( get_option( 'baspa_contacts_form_title' ) ) ) {
							echo wp_kses_post( get_option( 'baspa_contacts_form_title' ) );
						} else {
							echo wp_kses_post( __( 'Kontaktujte nás', 'baspa' ) );
						} ?>
					</h2>
					<?php if ( !empty( get_option( 'baspa_contacts_form_content' ) ) ) { ?>
						<p><?php echo wp_kses_post( wpautop( get_option( 'baspa_contacts_form_content' ) ) ); ?></p>
					<?php } ?>
				</header>

				<div class="f-off__form">
					<?php get_template_part( 'modules/contacts/templates/form', 'contact', array(
						'type' => 'thin',
					) ); ?>
				</div>
			</div>

		</div>
	</div>

</aside>
