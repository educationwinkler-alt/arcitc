<?php

/**
 * Form - Thanks
 */

if ( !empty( $GLOBALS[ 'arctic_contact_form_processed' ] ) ) {
	return;
}

$GLOBALS[ 'arctic_contact_form_processed' ] = true;

$is_local          = function_exists( 'wp_get_environment_type' ) && 'local' === wp_get_environment_type();
$suspicious        = false;
$recaptcha         = !empty( $_POST[ 'recaptcha' ] ) ? 'set' : ( $is_local ? 'local-skipped' : 'empty' ); // Token
$recaptcha_body    = '?';
$recaptcha_success = '?';
$recaptcha_score   = '?';

/**
 * Debug
 */
//	if ( is_user_logged_in() && is_super_admin() ) {
//		echo '<div class="f-alert" role="alert"><h5>Debug:</h5><br>';
//		echo '<pre>' . var_export( $_POST, true ) . '</pre>';
//		echo '</div>';
//	}

/**
 * Check
 */

// Check Nonce
if (
	( !isset( $_POST[ 'f-contact-nonce' ] ) || !wp_verify_nonce( $_POST[ 'f-contact-nonce' ], 'f-contact' ) ) &&
	( !isset( $_POST[ 'f-catalog-nonce' ] ) || !wp_verify_nonce( $_POST[ 'f-catalog-nonce' ], 'f-catalog' ) ) &&
	( !isset( $_POST[ 'f-service-nonce' ] ) || !wp_verify_nonce( $_POST[ 'f-service-nonce' ], 'f-service' ) )
) {
	get_template_part( 'modules/contacts/templates/form/alert/error' );
	error_log( get_the_date( get_option( 'date_format' ) ) . ' / ' . 'Nonce check failed!' );

	return;
}

// Check ReCAPTCHA
if ( $is_local ) {
	$recaptcha_body    = 'local environment';
	$recaptcha_success = 'skipped';
	$recaptcha_score   = 'skipped';

	if ( isset( $_POST[ 'f-form' ] ) && $_POST[ 'f-form' ] == 'catalog' ) {
		get_template_part( 'modules/contacts/templates/form/alert/success', 'catalog' );
	} else {
		get_template_part( 'modules/contacts/templates/form/alert/success' );
	}
} else if ( !empty( $_POST[ 'recaptcha' ] ) ) {
	$recaptcha_response = function_exists( 'forqy_form_recaptcha_get_response' ) ? forqy_form_recaptcha_get_response( $_POST[ 'recaptcha' ] ) : array();

	// Save reCAPTCHA params
	if ( !empty( $recaptcha_response ) ) {
		$recaptcha_body = $recaptcha_response;

		if ( isset( $recaptcha_response[ 'score' ] ) ) {
			$recaptcha_score = $recaptcha_response[ 'score' ];
		}
		if ( isset( $recaptcha_response[ 'success' ] ) ) {
			$recaptcha_success = $recaptcha_response[ 'success' ] ? 'true' : 'false';
		}

		if ( isset( $recaptcha_response[ 'score' ] ) && $recaptcha_response[ 'score' ] >= 0.4 ) {
			$suspicious = false;
			if ( $_POST[ 'f-form' ] == 'catalog' ) {
				get_template_part( 'modules/contacts/templates/form/alert/success', 'catalog' );
			} else {
				get_template_part( 'modules/contacts/templates/form/alert/success' );
			}
		} else {
			$suspicious = true; // Mark form as suspicious
			get_template_part( 'modules/contacts/templates/form/alert/error', 'recaptcha' );

			return;
		}
	} else {
		$suspicious = true; // Mark form as suspicious
	}
} else {
	$recaptcha_success = 'unset';
	get_template_part( 'modules/contacts/templates/form/alert/error', 'recaptcha' );

	return;
}

/**
 * Form Sent
 *
 * Hook: forqy_form_sent
 *
 * @hooked forqy_form_recaptcha_debug - 10
 */
do_action( 'forqy_form_sent' );

/**
 * Data
 */
$form      = isset( $_POST[ 'f-form' ] ) ? sanitize_key( $_POST[ 'f-form' ] ) : '';
$form_name = isset( $_POST[ 'f-form-name' ] ) ? sanitize_text_field( $_POST[ 'f-form-name' ] ) : '';
$number    = isset( $_POST[ 'f-number' ] ) ? sanitize_text_field( $_POST[ 'f-number' ] ) : '';
$title     = isset( $_POST[ 'f-title' ] ) ? sanitize_text_field( $_POST[ 'f-title' ] ) : '';
$url       = isset( $_POST[ 'f-url' ] ) ? esc_url_raw( $_POST[ 'f-url' ] ) : '';
$processed = function_exists( 'forqy_form_is_ajax' ) ? forqy_form_is_ajax() ? 'AJAX' : 'PHP' : '';

$name     = isset( $_POST[ 'f-name' ] ) ? sanitize_text_field( $_POST[ 'f-name' ] ) : '';
$email    = isset( $_POST[ 'f-email' ] ) ? sanitize_email( $_POST[ 'f-email' ] ) : '';
$phone    = isset( $_POST[ 'f-phone' ] ) ? sanitize_text_field( $_POST[ 'f-phone' ] ) : '';
$interest = isset( $_POST[ 'f-interest' ] ) ? sanitize_key( $_POST[ 'f-interest' ] ) : '';
$message  = isset( $_POST[ 'f-message' ] ) ? wp_kses_post( $_POST[ 'f-message' ] ) : '';
$terms    = isset( $_POST[ 'f-terms' ] ) ? sanitize_text_field( $_POST[ 'f-terms' ] ) : '';

/**
 * Insert Post
 */
$contact = wp_insert_post( array(
	'p'           => $number,
	'post_title'  => ( !empty( $name ) ? esc_html( $name ) : esc_html( $email ) ) . ( $suspicious ? __( ' — Suspicious', 'baspa' ) : '' ),
	'post_type'   => 'contact',
	'post_status' => 'publish',
) );

// Insert Meta
update_post_meta( $contact, 'contact_number', esc_html( $number ) );
update_post_meta( $contact, 'contact_recaptcha', sanitize_text_field( $recaptcha ) );

if ( !empty( $form ) ) {
	update_post_meta( $contact, 'contact_form', esc_html( $form ) );
}
if ( !empty( $form_name ) ) {
	update_post_meta( $contact, 'contact_form_name', esc_html( $form_name ) );
}
if ( !empty( $title ) ) {
	update_post_meta( $contact, 'contact_title', esc_html( $title ) );
}
if ( !empty( $url ) ) {
	update_post_meta( $contact, 'contact_url', esc_html( $url ) );
}
if ( !empty( $processed ) ) {
	update_post_meta( $contact, 'contact_processed', sanitize_text_field( $processed ) );
}

if ( !empty( $recaptcha_body ) ) {
	update_post_meta( $contact, 'contact_recaptcha_body', wp_json_encode( $recaptcha_body ) );
}
if ( !empty( $recaptcha_success ) ) {
	update_post_meta( $contact, 'contact_recaptcha_success', sanitize_text_field( $recaptcha_success ) );
}
if ( !empty( $recaptcha_score ) ) {
	update_post_meta( $contact, 'contact_recaptcha_score', sanitize_text_field( $recaptcha_score ) );
}

if ( !empty( $name ) ) {
	update_post_meta( $contact, 'contact_name', esc_html( $name ) );
}
if ( !empty( $email ) ) {
	update_post_meta( $contact, 'contact_email', esc_html( $email ) );
}
if ( !empty( $phone ) ) {
	update_post_meta( $contact, 'contact_phone', esc_html( $phone ) );
}
if ( !empty( $interest ) ) {
	update_post_meta( $contact, 'contact_interest', esc_html( $interest ) );
}
if ( !empty( $message ) ) {
	update_post_meta( $contact, 'contact_message', wp_kses_post( $message ) );
}

/**
 * Send Email
 */
// From
$from_name  = sanitize_text_field( get_theme_mod( 'baspa_form_from_name', get_bloginfo( 'name' ) ) );
$from_email = sanitize_email( get_theme_mod( 'baspa_form_from', get_bloginfo( 'admin_email' ) ) );
if ( !is_email( $from_email ) ) {
	$from_email = sanitize_email( get_bloginfo( 'admin_email' ) );
}
// Recipients
$recipient  = sanitize_email( get_theme_mod( 'baspa_form_to', get_bloginfo( 'admin_email' ) ) );
$recipients = array_filter( array( $recipient ), 'is_email' );
if ( isset( $interest ) && $interest == 'pool' && !empty( get_theme_mod( 'baspa_form_to_pool' ) ) ) {
	$recipients[] = sanitize_email( get_theme_mod( 'baspa_form_to_pool' ) );
}
if ( isset( $interest ) && $interest == 'jacuzzi' && !empty( get_theme_mod( 'baspa_form_to_jacuzzi' ) ) ) {
	$recipients[] = sanitize_email( get_theme_mod( 'baspa_form_to_jacuzzi' ) );
}
if ( isset( $interest ) && $interest == 'service' && !empty( get_theme_mod( 'baspa_form_to_service' ) ) ) {
	$recipients[] = sanitize_email( get_theme_mod( 'baspa_form_to_service' ) );
}
$recipients = array_values( array_filter( $recipients, 'is_email' ) );
// Subject
if ( $form == 'catalog' ) {
	$subject = get_bloginfo( 'name' ) . ' — ' . sprintf( __( 'Catalog #%s', 'baspa' ), $number );
} else if ( $form == 'service' ) {
	$subject = get_bloginfo( 'name' ) . ' — ' . sprintf( __( 'Service #%s', 'baspa' ), $number );
} else {
	$subject = get_bloginfo( 'name' ) . ' — ' . sprintf( __( 'Contact #%s — %s', 'baspa' ), $number, esc_html( baspa_contacts_get_interest_title( $interest ) ) );
}

// Email Template
ob_start();
require_once get_theme_file_path( 'modules/contacts/templates/email/contact.php' );
$body = ob_get_clean();

// Headers
$headers   = array();
$headers[] = 'Content-Type: text/html; charset=UTF-8';
$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
if ( is_email( $email ) ) {
	$headers[] = 'Reply-To: ' . $email;
}

// Send email
if ( !$is_local && !empty( $recipients ) && $email !== 'test@test.com' ) {
	wp_mail( $recipients, $subject, $body, $headers );
}

/**
 * Add Subscriber to Ecomail
 */
if ( isset( $interest ) && $interest == 'pool' ) {
	baspa_contacts_add_contact_to_ecomail( $name, $email, $phone, $form_name, $url, esc_html( baspa_contacts_get_interest_title( $interest ) ) );
} else if ( isset( $interest ) && $interest == 'jacuzzi' ) {
	baspa_contacts_add_contact_to_ecomail( $name, $email, $phone, $form_name, $url, esc_html( baspa_contacts_get_interest_title( $interest ) ) );
} else if ( isset( $interest ) && $interest == 'service' ) {
	baspa_contacts_add_contact_to_ecomail( $name, $email, $phone, $form_name, $url, esc_html( baspa_contacts_get_interest_title( $interest ) ) );
} else {
	if ( $form == 'catalog' ) {
		baspa_contacts_add_contact_to_ecomail( $name, $email, $phone, $form_name, $url, esc_html__( 'Catalog', 'baspa' ) );
	} else {
		baspa_contacts_add_contact_to_ecomail( $name, $email, $phone, $form_name, $url );
	}
}
