<?php

/**
 * Service Form
 */

// Settings
$type   = $args[ 'type' ] ?? 'full';
$header = $args[ 'header' ] ?? false;
$GLOBALS['baspa_service_form_render_count'] = isset( $GLOBALS['baspa_service_form_render_count'] ) ? (int) $GLOBALS['baspa_service_form_render_count'] + 1 : 1;
$form_render_count = (int) $GLOBALS['baspa_service_form_render_count'];
$form_id = 'form-service-' . $form_render_count;
$field_id = static function( string $name ) use ( $form_id ): string {
	return sanitize_key( $name . '-' . $form_id );
};
$field_ids = array(
	'name'    => $field_id( 'f-name' ),
	'email'   => $field_id( 'f-email' ),
	'phone'   => $field_id( 'f-phone' ),
	'message' => $field_id( 'f-message' ),
);

// Header
$form_header_class = array( 'f-form__header' );
if ( !$header ) {
	$form_header_class[] = 'screen-reader-text';
}

if ( isset( $_POST[ 'f-form--submitted' ] ) ) {
	/**
	 * Form Processing
	 */
	get_template_part( 'modules/contacts/templates/form/processing' );
} else { ?>

	<form id="<?php echo esc_attr( $form_id ); ?>"
	      class="f-form--service f-form js-form" method="post"
	      action="<?php echo esc_url( get_the_permalink() . '#' . $form_id ); ?>"
	      data-content-source="contact-settings"
	      data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">

		<div class="f-form__loading js-form__loading" aria-hidden="true">
			<?php forqy_get_icon( 'loader/puff' ); ?>
		</div>

		<div class="f-form__response js-form__response"></div>

		<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $form_header_class ); ?>>
			<h2><?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'service_header', __( 'Servisní formulář', 'baspa' ) ) : __( 'Servisní formulář', 'baspa' ) ); ?></h2>
		</header>

		<div class="a-flex a-gap--xs a-gap--m:m a-gap-row--0 a-gap-row--0:m">
			<div class="a-flex__item--100<?php if ( $type == 'full' ) { ?> a-flex__item--33:m<?php } ?>">

				<div class="f-field a-field">
					<label for="<?php echo esc_attr( $field_ids['name'] ); ?>" class="f-label">
						<?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'label_name', __( 'Jméno a příjmení', 'baspa' ) ) : __( 'Jméno a příjmení', 'baspa' ) ); ?>
						<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
						      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
					</label>
					<input type="text" id="<?php echo esc_attr( $field_ids['name'] ); ?>" name="f-name" class="f-input" autocomplete="name"
					       placeholder="<?php echo esc_attr( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'placeholder_name', __( 'Vyplňte jméno a příjmení ...', 'baspa' ) ) : __( 'Vyplňte jméno a příjmení ...', 'baspa' ) ); ?>"
					       value="<?php echo isset( $_POST[ 'f-name' ] ) ? esc_attr( wp_unslash( $_POST[ 'f-name' ] ) ) : ''; ?>" required>
				</div>

			</div>
			<div class="a-flex__item--100<?php if ( $type == 'full' ) { ?> a-flex__item--33:m<?php } ?>">

				<div class="f-field a-field">
					<label for="<?php echo esc_attr( $field_ids['email'] ); ?>" class="f-label">
						<?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'label_email', __( 'Email', 'baspa' ) ) : __( 'Email', 'baspa' ) ); ?>
						<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
						      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
					</label>
					<input type="email" id="<?php echo esc_attr( $field_ids['email'] ); ?>" name="f-email" class="f-input" autocomplete="email"
					       placeholder="<?php echo esc_attr( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'placeholder_email', __( 'Vyplňte e-mail ...', 'baspa' ) ) : __( 'Vyplňte e-mail ...', 'baspa' ) ); ?>"
					       value="<?php echo isset( $_POST[ 'f-email' ] ) ? esc_attr( wp_unslash( $_POST[ 'f-email' ] ) ) : ''; ?>" required>
				</div>

			</div>
			<div class="a-flex__item--100<?php if ( $type == 'full' ) { ?> a-flex__item--33:m<?php } ?>">

				<div class="f-field a-field">
					<label for="<?php echo esc_attr( $field_ids['phone'] ); ?>" class="f-label">
						<?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'label_phone', __( 'Telefon', 'baspa' ) ) : __( 'Telefon', 'baspa' ) ); ?>
						<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
						      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
					</label>
					<input type="tel" id="<?php echo esc_attr( $field_ids['phone'] ); ?>" name="f-phone" class="f-input" autocomplete="tel"
					       placeholder="<?php echo esc_attr( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'placeholder_phone', __( 'Vyplňte telefon ...', 'baspa' ) ) : __( 'Vyplňte telefon ...', 'baspa' ) ); ?>"
					       value="<?php echo isset( $_POST[ 'f-phone' ] ) ? esc_attr( wp_unslash( $_POST[ 'f-phone' ] ) ) : ''; ?>" required>
				</div>

			</div>
			<div class="a-flex__item--100">

				<div class="f-field a-field">
					<label for="<?php echo esc_attr( $field_ids['message'] ); ?>" class="f-label">
						<?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'label_service_message', __( 'Dotaz nebo servisní požadavek', 'baspa' ) ) : __( 'Dotaz nebo servisní požadavek', 'baspa' ) ); ?>
					</label>
					<textarea id="<?php echo esc_attr( $field_ids['message'] ); ?>"
					          name="f-message"
					          placeholder="<?php echo esc_attr( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'placeholder_service_message', __( 'Napište dotaz nebo servisní požadavek ...', 'baspa' ) ) : __( 'Napište dotaz nebo servisní požadavek ...', 'baspa' ) ); ?>"
					          class="f-input f-textarea"><?php echo isset( $_POST[ 'f-message' ] ) ? esc_textarea( wp_unslash( $_POST[ 'f-message' ] ) ) : ''; ?></textarea>
				</div>

			</div>
			<div class="a-flex__item--100">

				<?php
				/**
				 * Before Submit
				 *
				 * Hook: forqy_form_submit_before
				 *
				 * @hooked forqy_form_recaptcha_input - 5
				 * @hooked forqy_form_recaptcha_debug - 10
				 */
				do_action( 'forqy_form_submit_before' );
				?>

				<div class="f-form__submit a-flex a-flex--align-center a-gap--m">
					<?php if ( function_exists( 'forqy_privacy_page_exists' ) && forqy_privacy_page_exists() && get_privacy_policy_url() ) { ?>
						<div class="a-flex__item--100 a-flex__item--auto:m">

							<div class="f-form__note">
								<?php echo sprintf( __( 'Odesláním souhlasíte s podmínkami <a href="%s" target="_blank">ochrany osobních údajů</a>.', 'baspa' ), get_privacy_policy_url() ) ?>
							</div>

						</div>
					<?php } ?>
					<div class="a-flex__item--100 a-flex__item:m">

						<button type="submit" name="f-form--submitted" value="true"
						        class="f-form--submit a-button a-button--accent">
							<?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'submit_service', __( 'Odeslat požadavek', 'baspa' ) ) : __( 'Odeslat požadavek', 'baspa' ) ); ?>
						</button>

					</div>
				</div>

			</div>
		</div>

		<input type="hidden" name="f-form" value="service">
		<input type="hidden" name="f-form-name" value="<?php echo esc_attr__( 'Servis', 'baspa' ); ?>">
		<input type="hidden" name="f-number" value="<?php echo function_exists( 'forqy_form_get_number' ) ? forqy_form_get_number() : ''; ?>">
		<input type="hidden" name="f-title" value="<?php echo function_exists( 'forqy_get_current_object_title' ) ? forqy_get_current_object_title() : ''; ?>">
		<input type="hidden" name="f-url" value="<?php echo function_exists( 'forqy_get_current_object_url' ) ? forqy_get_current_object_url() : ''; ?>">
		<input type="hidden" name="f-interest" value="service">

		<input type="hidden" name="f-service-nonce" value="<?php echo esc_attr( wp_create_nonce( 'f-service' ) ); ?>">
	</form>

<?php }
