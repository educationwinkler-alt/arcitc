<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_contacts_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array<string> $meta_boxes
	 *
	 * @return array<string>
	 */
	function baspa_contacts_metabox_register( array $meta_boxes ): array {

		$post_id = $_GET[ 'post' ] ?? null;

		if ( empty( $post_id ) ) {
			return $meta_boxes;
		}

		$form = get_post_meta( $post_id, 'contact_form', true );

		/**
		 * Basic Information
		 */
		$information_basic_fields = array(
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Name', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_name', true ) ?: '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Email', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_email', true ) ?: '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Phone', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_phone', true ) ?: '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Interest', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_interest', true ) ? baspa_contacts_get_interest_title( get_post_meta( $post_id, 'contact_interest', true ) ) : '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Message', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_message', true ) ?: '&mdash;' ) . '</span></div>',
			),
		);
		// Add Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--information-basic',
			'title'      => esc_html__( 'Basic Information', 'baspa' ),
			'post_types' => array( 'contact' ),
			'fields'     => $information_basic_fields,
		);

		$jucra_model = get_post_meta( $post_id, 'contact_jucra_model', true );
		if ( !empty( $jucra_model ) ) {
			$jucra_options = json_decode( (string)get_post_meta( $post_id, 'contact_jucra_options', true ), true );
			$jucra_fields  = array(
				array(
					'type' => 'custom_html',
					'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Model', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( $jucra_model ) . '</span></div>',
				),
			);

			if ( is_array( $jucra_options ) ) {
				foreach ( $jucra_options as $option ) {
					$title = isset( $option['title'] ) ? (string)$option['title'] : '';
					$label = isset( $option['label'] ) ? (string)$option['label'] : '';

					if ( $title === '' && $label === '' ) {
						continue;
					}

					$jucra_fields[] = array(
						'type' => 'custom_html',
						'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html( $title ) . '</strong><span class="f-metabox__value">' . esc_html( $label ?: '&mdash;' ) . '</span></div>',
					);
				}
			}

			$builder_url = get_post_meta( $post_id, 'contact_jucra_builder_url', true );
			if ( !empty( $builder_url ) ) {
				$jucra_fields[] = array(
					'type' => 'custom_html',
					'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Builder URL', 'baspa' ) . '</strong><span class="f-metabox__value"><a href="' . esc_url( $builder_url ) . '" target="_blank" rel="noopener">' . esc_html( $builder_url ) . '</a></span></div>',
				);
			}

			$meta_boxes[] = array(
				'id'         => 'baspa-metabox--jucra-configuration',
				'title'      => esc_html__( '3D konfigurace', 'baspa' ),
				'post_types' => array( 'contact' ),
				'fields'     => $jucra_fields,
			);
		}

		/**
		 * Additional Information
		 */
		$information_additional_fields = array(
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Form', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_form_name', true ) ?: '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Title', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_title', true ) ?: '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'URL', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_url', true ) ?: '&mdash;' ) . '</span></div>',
			),
		);
		// Add Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--information-additional',
			'title'      => esc_html__( 'Additional Information', 'baspa' ),
			'post_types' => array( 'contact' ),
			'fields'     => $information_additional_fields,
		);

		/**
		 * ReCAPTCHA
		 */
		$recaptcha_fields = array(
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Token', 'baspa' ) . '</strong><span class="f-metabox__value" style="word-break: break-all;">' . esc_html( get_post_meta( $post_id, 'contact_recaptcha', true ) ?: '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Success', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_recaptcha_success', true ) ?: '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Score', 'baspa' ) . '</strong><span class="f-metabox__value">' . esc_html( get_post_meta( $post_id, 'contact_recaptcha_score', true ) ?: '&mdash;' ) . '</span></div>',
			),
			array(
				'type' => 'custom_html',
				'std'  => '<div class="f-metabox__content"><strong class="f-metabox__label">' . esc_html__( 'Body', 'baspa' ) . '</strong><span class="f-metabox__value">' . get_post_meta( $post_id, 'contact_recaptcha_body', true ) ?: '&mdash;' . '</span></div>',
			),
		);
		// Add Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--recaptcha',
			'title'      => esc_html__( 'ReCAPTCHA', 'baspa' ),
			'post_types' => array( 'contact' ),
			'fields'     => $recaptcha_fields,
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_contacts_metabox_register' );

}
