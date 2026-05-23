<?php

/**
 * Catalog Form
 */

if ( isset( $_POST[ 'f-form--submitted' ] ) ) {
	/**
	 * Form Processing
	 */
	get_template_part( 'modules/contacts/templates/form/processing' );
} else { ?>

	<form id="<?php echo sanitize_title( esc_attr_x( 'form-catalog', 'anchor', 'baspa' ) ); ?>"
	      class="f-form--catalog f-form js-form" method="post"
	      action="<?php echo esc_url( get_the_permalink() . '#' . _x( 'form-catalog', 'anchor', 'baspa' ) ); ?>"
	      data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">

		<div class="f-form__loading js-form__loading" aria-hidden="true">
			<?php forqy_get_icon( 'loader/puff' ); ?>
		</div>

		<div class="f-form__response js-form__response"></div>

		<div class="a-flex a-flex--align-center a-gap--xxs">
			<div class="a-flex__item">

				<div class="f-field a-field">
					<label for="f-email" class="f-label screen-reader-text">
						<?php echo esc_html__( 'Email', 'baspa' ); ?>
						<abbr title="<?php echo esc_attr__( 'Povinné', 'baspa' ); ?>"
						      class="f-required"><?php echo esc_html__( '&#10043;', 'baspa' ); ?></abbr>
					</label>
					<input type="email" id="f-email" name="f-email" class="f-input" autocomplete="email"
					       placeholder="<?php echo esc_attr__( 'Vyplňte e-mail', 'baspa' ); ?> ..."
					       value="<?php echo isset( $_POST[ 'f-email' ] ) ? esc_attr( wp_unslash( $_POST[ 'f-email' ] ) ) : ''; ?>" aria-required="true" required>
				</div>

			</div>
			<div class="a-flex__item">

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

				<button type="submit" name="f-form--submitted" value="true" class="f-form--submit a-button a-button--accent">
					<?php echo esc_html__( 'Odeslat', 'baspa' ) ?>
				</button>

			</div>
			<?php if ( function_exists( 'forqy_privacy_page_exists' ) && forqy_privacy_page_exists() && get_privacy_policy_url() ) { ?>
				<div class="a-flex__item--100 a-flex__item--auto:m">

					<div class="f-form__note">
						<?php echo sprintf( __( 'Odesláním souhlasíte s podmínkami <a href="%s" target="_blank">ochrany osobních údajů</a>.', 'baspa' ), get_privacy_policy_url() ) ?>
					</div>

				</div>
			<?php } ?>
		</div>

		<input type="hidden" name="f-form" value="catalog">
		<input type="hidden" name="f-form-name" value="<?php echo esc_attr__( 'Katalog', 'baspa' ); ?>">
		<input type="hidden" name="f-number" value="<?php echo function_exists( 'forqy_form_get_number' ) ? forqy_form_get_number() : ''; ?>">
		<input type="hidden" name="f-title" value="<?php echo function_exists( 'forqy_get_current_object_title' ) ? forqy_get_current_object_title() : ''; ?>">
		<input type="hidden" name="f-url" value="<?php echo function_exists( 'forqy_get_current_object_url' ) ? forqy_get_current_object_url() : ''; ?>">

		<?php wp_nonce_field( 'f-catalog', 'f-catalog-nonce' ); ?>
	</form>

<?php }
