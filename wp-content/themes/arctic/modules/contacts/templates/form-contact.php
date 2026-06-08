<?php

/**
 * Contact Form
 */

// Settings
$type   = $args[ 'type' ] ?? 'full';
$header = $args[ 'header' ] ?? false;
$context         = sanitize_key( $args['context'] ?? 'contact' );
$is_jucra        = $context === 'jucra';
$jucra_selection = $is_jucra && isset( $args['jucra_selection'] ) && is_array( $args['jucra_selection'] ) ? $args['jucra_selection'] : array();
static $form_render_count = 0;
$form_render_count++;
$form_id = 'form-contact-' . sanitize_key( $context . '-' . $form_render_count );
$field_id = static function( string $name ) use ( $form_id ): string {
	return sanitize_key( $name . '-' . $form_id );
};
$field_ids = array(
	'name'     => $field_id( 'f-name' ),
	'email'    => $field_id( 'f-email' ),
	'phone'    => $field_id( 'f-phone' ),
	'interest' => $field_id( 'f-interest' ),
	'message'  => $field_id( 'f-message' ),
);
$form_action     = $is_jucra && function_exists( 'arctic_jucra_get_inquiry_url' )
	? arctic_jucra_get_inquiry_url() . '#' . $form_id
	: get_the_permalink() . '#' . $form_id;
$form_value      = $is_jucra ? 'jucra' : 'contact';
$form_name       = $is_jucra ? __( '3D konfigurátor', 'baspa' ) : __( 'Kontakt', 'baspa' );
$form_title      = $is_jucra ? __( 'Poptávka konfigurace', 'baspa' ) : ( function_exists( 'forqy_get_current_object_title' ) ? forqy_get_current_object_title() : '' );
$request_uri     = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string)$_SERVER['REQUEST_URI'] ) : '/';
$form_url        = $is_jucra ? home_url( $request_uri ) : ( function_exists( 'forqy_get_current_object_url' ) ? forqy_get_current_object_url() : '' );
$submit_label    = function_exists( 'baspa_contacts_form_text' )
	? baspa_contacts_form_text( $is_jucra ? 'submit_jucra' : 'submit_contact', $is_jucra ? __( 'Odeslat poptávku', 'baspa' ) : __( 'Odeslat', 'baspa' ) )
	: ( $is_jucra ? __( 'Odeslat poptávku', 'baspa' ) : __( 'Odeslat', 'baspa' ) );
$jucra_options   = isset( $jucra_selection['options'] ) && is_array( $jucra_selection['options'] ) ? $jucra_selection['options'] : array();
$jucra_hidden    = array();

if ( $is_jucra ) {
	$jucra_hidden['f-jucra-model']       = isset( $jucra_selection['model_name'] ) ? (string)$jucra_selection['model_name'] : '';
	$jucra_hidden['f-jucra-builder-url'] = isset( $jucra_selection['builder_url'] ) ? (string)$jucra_selection['builder_url'] : '';

	foreach ( array( 'jets', 'acrylic', 'cabinet' ) as $option_key ) {
		$value = isset( $jucra_options[ $option_key ]['value'] ) && is_array( $jucra_options[ $option_key ]['value'] ) ? $jucra_options[ $option_key ]['value'] : array();

		$jucra_hidden[ 'f-jucra-option-' . $option_key ]            = isset( $value['id'] ) ? (string)$value['id'] : '';
		$jucra_hidden[ 'f-jucra-option-' . $option_key . '-label' ] = isset( $value['label'] ) ? (string)$value['label'] : '';
		$jucra_hidden[ 'f-jucra-option-' . $option_key . '-icon' ]  = isset( $value['icon_url'] ) ? (string)$value['icon_url'] : '';
		$jucra_hidden[ 'f-jucra-option-' . $option_key . '-title' ] = isset( $jucra_options[ $option_key ]['title'] ) ? (string)$jucra_options[ $option_key ]['title'] : '';
	}
}

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
	      class="f-form--contact<?php echo $is_jucra ? ' f-form--jucra-inquiry' : ''; ?> f-form js-form" method="post"
	      action="<?php echo esc_url( $form_action ); ?>"
	      data-content-source="contact-settings"
	      data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">

		<div class="f-form__loading js-form__loading" aria-hidden="true">
			<?php forqy_get_icon( 'loader/puff' ); ?>
		</div>

		<div class="f-form__response js-form__response"></div>

		<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $form_header_class ); ?>>
			<h2><?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'contact_header', __( 'Kontaktní formulář', 'baspa' ) ) : __( 'Kontaktní formulář', 'baspa' ) ); ?></h2>
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
			<?php if ( function_exists( 'baspa_contacts_form_interest_options' ) ) {
				$interest_options = baspa_contacts_form_interest_options();
				if ( !empty( $interest_options ) ) { ?>
					<div class="a-flex__item--100<?php if ( $type == 'full' ) { ?> a-flex__item--100:m<?php } ?>">

						<div class="f-field a-field">
							<label for="<?php echo esc_attr( $field_ids['interest'] ); ?>" class="f-label">
								<?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'label_interest', __( 'O co máte zájem?', 'baspa' ) ) : __( 'O co máte zájem?', 'baspa' ) ); ?>
								<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
								      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
							</label>
							<?php if ( $is_jucra ) { ?>
								<input type="hidden" id="<?php echo esc_attr( $field_ids['interest'] ); ?>" name="f-interest" value="jacuzzi">
								<div class="f-jucra-inquiry__interest-note">
									<?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'jucra_interest_note', __( 'Typ poptávky: vířivka z 3D konfigurátoru', 'baspa' ) ) : __( 'Typ poptávky: vířivka z 3D konfigurátoru', 'baspa' ) ); ?>
								</div>
							<?php } else { ?>
								<select id="<?php echo esc_attr( $field_ids['interest'] ); ?>" name="f-interest" class="f-select" required>
									<option value
									        disabled
									        selected><?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'placeholder_interest', __( 'Vyberte, o co máte zájem ...', 'baspa' ) ) : __( 'Vyberte, o co máte zájem ...', 'baspa' ) ); ?>
									</option>
									<?php foreach ( $interest_options as $key => $label ) { ?>
										<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
									<?php } ?>
								</select>
							<?php } ?>
						</div>

					</div>
				<?php }
			} ?>
			<div class="a-flex__item--100">

				<div class="f-field a-field">
					<label for="<?php echo esc_attr( $field_ids['message'] ); ?>" class="f-label">
						<?php echo esc_html( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'label_message', __( 'Dotaz nebo poptávka', 'baspa' ) ) : __( 'Dotaz nebo poptávka', 'baspa' ) ); ?>
					</label>
					<textarea id="<?php echo esc_attr( $field_ids['message'] ); ?>"
					          name="f-message"
					          placeholder="<?php echo esc_attr( function_exists( 'baspa_contacts_form_text' ) ? baspa_contacts_form_text( 'placeholder_message', __( 'Napište dotaz nebo poptávku ...', 'baspa' ) ) : __( 'Napište dotaz nebo poptávku ...', 'baspa' ) ); ?>"
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
							<?php echo esc_html( $submit_label ) ?>
						</button>

					</div>
				</div>

			</div>
		</div>

		<input type="hidden" name="f-form" value="<?php echo esc_attr( $form_value ); ?>">
		<input type="hidden" name="f-form-name" value="<?php echo esc_attr( $form_name ); ?>">
		<input type="hidden" name="f-number" value="<?php echo function_exists( 'forqy_form_get_number' ) ? forqy_form_get_number() : ''; ?>">
		<input type="hidden" name="f-title" value="<?php echo esc_attr( $form_title ); ?>">
		<input type="hidden" name="f-url" value="<?php echo esc_url( $form_url ); ?>">
		<?php foreach ( $jucra_hidden as $hidden_name => $hidden_value ) { ?>
			<input type="hidden" name="<?php echo esc_attr( $hidden_name ); ?>" value="<?php echo esc_attr( $hidden_value ); ?>">
		<?php } ?>

		<input type="hidden" name="f-contact-nonce" value="<?php echo esc_attr( wp_create_nonce( 'f-contact' ) ); ?>">
	</form>

<?php }
