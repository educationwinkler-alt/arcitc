<?php

/*

Contact Form 7

*/

if ( ! function_exists( 'baspa_cf7_form_default' ) ) {

	/**
	 * Default CF7 Form Template
	 *
	 * @param $template
	 * @param $prop
	 *
	 * @return mixed|string
	 */
	function baspa_cf7_form_default( $template, $prop ): mixed {

		if ( 'form' == $prop ) {
			$template = '<div class="f-form f-form--cf7 a-flex a-gap-row--0 a-gap-col--m">' . "\n";
			$template .= '	<div class="a-flex__item--100">' . "\n";
			$template .= '		<div class="f-field a-field">' . "\n";
			$template .= '			<label for="your-name" class="f-label a-label">' . esc_html_x( 'Name and surname', 'form', 'baspa' ) . ' <abbr title="' . esc_html_x( 'Required', 'form', 'baspa' ) . '">&#10043;</abbr></label>' . "\n";
			$template .= '          [text* your-name id:your-name placeholder "' . esc_html_x( 'Fill in your name', 'form', 'baspa' ) . ' ..."]' . "\n";
			$template .= '		</div>' . "\n";
			$template .= '	</div>' . "\n";
			$template .= '	<div class="a-flex__item--100">' . "\n";
			$template .= '		<div class="f-field a-field">' . "\n";
			$template .= '			<label for="your-email" class="f-label a-label">' . esc_html_x( 'Email', 'form', 'baspa' ) . ' <abbr title="' . esc_html_x( 'Required', 'form', 'baspa' ) . '">&#10043;</abbr></label>' . "\n";
			$template .= '          [email* your-email id:your-email placeholder "' . esc_html_x( 'Fill in your email', 'form', 'baspa' ) . ' ..."]' . "\n";
			$template .= '		</div>' . "\n";
			$template .= '	</div>' . "\n";
			$template .= '	<div class="a-flex__item--100">' . "\n";
			$template .= '		<div class="f-field a-field">' . "\n";
			$template .= '			<label for="your-phone" class="f-label a-label">' . esc_html_x( 'Phone', 'form', 'baspa' ) . ' <abbr title="' . esc_html_x( 'Required', 'form', 'baspa' ) . '">&#10043;</abbr></label>' . "\n";
			$template .= '          [text* your-phone id:your-phone placeholder "' . esc_html_x( 'Fill in your phone', 'form', 'baspa' ) . ' ..."]' . "\n";
			$template .= '		</div>' . "\n";
			$template .= '	</div>' . "\n";
			$template .= '	<div class="a-flex__item--100">' . "\n";
			$template .= '		<div class="f-field f-field--message a-field">' . "\n";
			$template .= '			<label for="your-message" class="f-label a-label">' . esc_html_x( 'Message', 'form', 'baspa' ) . '</label>' . "\n";
			$template .= '          [textarea your-message 40x3 id:your-message placeholder "' . esc_html_x( 'Fill in your message', 'form', 'baspa' ) . ' ..."]' . "\n";
			$template .= '		</div>' . "\n";
			$template .= '	</div>' . "\n";
			$template .= '	<div class="a-flex__item--100">' . "\n";
			$template .= '		<div class="f-form__terms a-form__terms">' . "\n";
			$template .= '          ' . esc_html_x( 'By submitting the form, you agree to the processing of personal data necessary to handle your inquiry.', 'form', 'baspa' ) . "\n";
			$template .= '		</div>' . "\n";
			$template .= '	</div>' . "\n";
			$template .= '	<div class="a-flex__item--100">' . "\n";
			$template .= '		<div class="f-form__submit">' . "\n";
			$template .= '          [response]' . "\n";
			$template .= '          [submit "' . esc_html_x( 'Send', 'form', 'baspa' ) . '"]' . "\n";
			$template .= '		</div>' . "\n";
			$template .= '	</div>' . "\n";
			$template .= '</div>' . "\n";
		}

		return $template;

	}

	add_filter( 'wpcf7_default_template', 'baspa_cf7_form_default', 10, 2 );

}

/**
 * Remove <p> tags
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );
