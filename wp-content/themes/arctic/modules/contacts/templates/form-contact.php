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
$form_action     = $is_jucra && function_exists( 'arctic_jucra_get_inquiry_url' )
	? arctic_jucra_get_inquiry_url() . '#' . _x( 'form-contact', 'anchor', 'baspa' )
	: get_the_permalink() . '#' . _x( 'form-contact', 'anchor', 'baspa' );
$form_value      = $is_jucra ? 'jucra' : 'contact';
$form_name       = $is_jucra ? __( '3D konfigurátor', 'baspa' ) : __( 'Kontakt', 'baspa' );
$form_title      = $is_jucra ? __( 'Poptávka konfigurace', 'baspa' ) : ( function_exists( 'forqy_get_current_object_title' ) ? forqy_get_current_object_title() : '' );
$request_uri     = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string)$_SERVER['REQUEST_URI'] ) : '/';
$form_url        = $is_jucra ? home_url( $request_uri ) : ( function_exists( 'forqy_get_current_object_url' ) ? forqy_get_current_object_url() : '' );
$submit_label    = $is_jucra ? __( 'Odeslat poptávku', 'baspa' ) : __( 'Odeslat', 'baspa' );
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

	<form id="<?php echo sanitize_title( esc_attr_x( 'form-contact', 'anchor', 'baspa' ) ); ?>"
	      class="f-form--contact<?php echo $is_jucra ? ' f-form--jucra-inquiry' : ''; ?> f-form js-form" method="post"
	      action="<?php echo esc_url( $form_action ); ?>"
	      data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">

		<div class="f-form__loading js-form__loading" aria-hidden="true">
			<?php forqy_get_icon( 'loader/puff' ); ?>
		</div>

		<div class="f-form__response js-form__response"></div>

		<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $form_header_class ); ?>>
			<h2><?php echo esc_html__( 'Kontaktní formulář', 'baspa' ); ?></h2>
		</header>

		<div class="a-flex a-gap--xs a-gap--m:m a-gap-row--0 a-gap-row--0:m">
			<div class="a-flex__item--100<?php if ( $type == 'full' ) { ?> a-flex__item--33:m<?php } ?>">

				<div class="f-field a-field">
					<label for="f-name" class="f-label">
						<?php echo esc_html__( 'Jméno a příjmení', 'baspa' ); ?>
						<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
						      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
					</label>
					<input type="text" id="f-name" name="f-name" class="f-input" autocomplete="name"
					       placeholder="<?php echo esc_attr__( 'Vyplňte jméno a příjmení', 'baspa' ); ?> ..."
					       value="<?php echo isset( $_POST[ 'f-name' ] ) ? esc_attr( wp_unslash( $_POST[ 'f-name' ] ) ) : ''; ?>" aria-required="true" required>
				</div>

			</div>
			<div class="a-flex__item--100<?php if ( $type == 'full' ) { ?> a-flex__item--33:m<?php } ?>">

				<div class="f-field a-field">
					<label for="f-email" class="f-label">
						<?php echo esc_html__( 'Email', 'baspa' ); ?>
						<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
						      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
					</label>
					<input type="email" id="f-email" name="f-email" class="f-input" autocomplete="email"
					       placeholder="<?php echo esc_attr__( 'Vyplňte e-mail', 'baspa' ); ?> ..."
					       value="<?php echo isset( $_POST[ 'f-email' ] ) ? esc_attr( wp_unslash( $_POST[ 'f-email' ] ) ) : ''; ?>" aria-required="true" required>
				</div>

			</div>
			<div class="a-flex__item--100<?php if ( $type == 'full' ) { ?> a-flex__item--33:m<?php } ?>">

				<div class="f-field a-field">
					<label for="f-phone" class="f-label">
						<?php echo esc_html__( 'Telefon', 'baspa' ); ?>
						<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
						      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
					</label>
					<input type="tel" id="f-phone" name="f-phone" class="f-input" autocomplete="tel"
					       placeholder="<?php echo esc_attr__( 'Vyplňte telefon', 'baspa' ); ?> ..."
					       value="<?php echo isset( $_POST[ 'f-phone' ] ) ? esc_attr( wp_unslash( $_POST[ 'f-phone' ] ) ) : ''; ?>" aria-required="true" required>
				</div>

			</div>
			<?php if ( function_exists( 'baspa_contacts_form_interest_options' ) ) {
				$interest_options = baspa_contacts_form_interest_options();
				if ( !empty( $interest_options ) ) { ?>
					<div class="a-flex__item--100<?php if ( $type == 'full' ) { ?> a-flex__item--100:m<?php } ?>">

						<div class="f-field a-field">
							<label for="f-interest" class="f-label">
								<?php echo esc_html__( 'O co máte zájem?', 'baspa' ); ?>
								<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
								      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
							</label>
							<?php if ( $is_jucra ) { ?>
								<input type="hidden" id="f-interest" name="f-interest" value="jacuzzi">
								<div class="f-jucra-inquiry__interest-note">
									<?php echo esc_html__( 'Typ poptávky: vířivka z 3D konfigurátoru', 'baspa' ); ?>
								</div>
							<?php } else { ?>
								<select id="f-interest" name="f-interest" class="f-select" aria-required="true" required>
									<option value
									        disabled
									        selected><?php echo esc_html__( 'Vyberte, o co máte zájem', 'baspa' ); ?> ...
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
					<label for="f-message" class="f-label">
						<?php echo esc_html__( 'Dotaz nebo poptávka', 'baspa' ); ?>
					</label>
					<textarea id="f-message"
					          name="f-message"
					          placeholder="<?php echo esc_attr__( 'Napište dotaz nebo poptávku', 'baspa' ); ?> ..."
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

		<?php wp_nonce_field( 'f-contact', 'f-contact-nonce' ); ?>
	</form>

<?php }
