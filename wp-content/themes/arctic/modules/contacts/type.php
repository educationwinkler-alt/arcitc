<?php

/**
 * Type
 */

if ( !function_exists( 'baspa_contacts_type_register' ) ) {

	/**
	 * Register Type
	 */
	function baspa_contacts_type_register(): void {

		register_post_type( 'contact', array(
			'labels'              => array(
				'name'               => esc_html_x( 'Contacts', 'type', 'baspa' ),
				'menu_name'          => esc_html_x( 'Contacts', 'type', 'baspa' ),
				'singular_name'      => esc_html_x( 'Contact', 'type', 'baspa' ),
				'name_admin_bar'     => esc_html_x( 'Add Contact', 'type', 'baspa' ),
				'all_items'          => esc_html_x( 'All Contacts', 'type', 'baspa' ),
				'add_new'            => esc_html_x( 'Add Contact', 'type', 'baspa' ),
				'add_new_item'       => esc_html_x( 'Add Contact', 'type', 'baspa' ),
				'edit_item'          => esc_html_x( 'Edit Contact', 'type', 'baspa' ),
				'new_item'           => esc_html_x( 'New Contact', 'type', 'baspa' ),
				'view_item'          => esc_html_x( 'View Contact', 'type', 'baspa' ),
				'view_items'         => esc_html_x( 'View Contacts', 'type', 'baspa' ),
				'search_items'       => esc_html_x( 'Search Contacts', 'type', 'baspa' ),
				'not_found'          => esc_html_x( 'No Contacts', 'type', 'baspa' ),
				'not_found_in_trash' => esc_html_x( 'No Contacts Found in Trash', 'type', 'baspa' ),
				'archives'           => esc_html_x( 'Contact Archives', 'type', 'baspa' ),
				'attributes'         => esc_html_x( 'Contact Attributes', 'type', 'baspa' ),
				'item_published'     => esc_html_x( 'Contact saved.', 'type', 'baspa' ),
				'item_updated'       => esc_html_x( 'Contact updated.', 'type', 'baspa' ),
			),
			'capability_type'     => 'page',
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'        => true,
			'public'              => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'hierarchical'        => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'query_var'           => true,
			'rewrite'             => array(
				'slug'       => _x( 'contacts', 'type', 'baspa' ),
				'with_front' => false,
			),
			'menu_position'       => 40,
			'menu_icon'           => 'dashicons-email-alt2',
			'supports'            => array(
				'title',
			),
		) );

	}

	add_action( 'init', 'baspa_contacts_type_register' );

}

if ( !function_exists( 'baspa_contacts_type_columns' ) ) {

	/**
	 * Admin Columns
	 *
	 * @return array<string>
	 */
	function baspa_contacts_type_columns(): array {

		return array(
			'cb'        => "<input type=\"checkbox\">",
			'title'     => esc_html_x( 'Contact', 'admin', 'baspa' ),
			'form'      => esc_html_x( 'Form', 'admin', 'baspa' ),
			'interest'  => esc_html_x( 'Interest', 'admin', 'baspa' ),
			'email'     => esc_html_x( 'Email', 'admin', 'baspa' ),
			'phone'     => esc_html_x( 'Phone', 'admin', 'baspa' ),
			'page'      => esc_html_x( 'Page', 'admin', 'baspa' ),
			'recaptcha' => esc_html_x( 'ReCAPTCHA', 'admin', 'baspa' ),
			'received'  => esc_html_x( 'Received', 'admin', 'baspa' ),
		);

	}

	add_filter( 'manage_edit-contact_columns', 'baspa_contacts_type_columns' );

}

if ( !function_exists( 'baspa_contacts_type_columns_content' ) ) {

	/**
	 * Admin Columns Content
	 *
	 * @param string $column
	 */
	function baspa_contacts_type_columns_content( string $column ): void {
		global $post;

		switch ( $column ) {

			// Form
			case 'form':

				$form = get_post_meta( $post->ID, 'contact_form_name', true );

				if ( !empty( $form ) ) {
					echo esc_html( $form );
				}

				break;

			// Interest
			case 'interest':

				$interest = get_post_meta( $post->ID, 'contact_interest', true );

				if ( !empty( $interest ) ) {
					echo esc_html( baspa_contacts_get_interest_title( $interest ) );
				}

				break;

			// Email
			case 'email':

				$email = get_post_meta( $post->ID, 'contact_email', true );

				if ( !empty( $email ) ) {
					echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
				}

				break;

			// Phone
			case 'phone':

				$phone = get_post_meta( $post->ID, 'contact_phone', true );

				if ( !empty( $phone ) ) {
					echo '<a href="tel:' . str_replace( ' ', '', esc_attr( $phone ) ) . '">' . esc_html( $phone ) . '</a>';
				}

				break;

			// Page
			case 'page':

				$title = get_post_meta( $post->ID, 'contact_title', true );
				$url   = get_post_meta( $post->ID, 'contact_url', true );

				if ( !empty( $title ) && !empty( $url ) ) {
					echo '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $title ) . '</a>';
				}

				break;

			// ReCAPTCHA
			case 'recaptcha':

				$recaptcha = array(
					get_post_meta( $post->ID, 'contact_recaptcha_success', true ),
					get_post_meta( $post->ID, 'contact_recaptcha_score', true ),
				);

				echo ucfirst( implode( ' &mdash; ', $recaptcha ) );

				break;

			// Received
			case 'received':

				$date = get_the_time( 'j. n. Y (H:i)', $post->ID );

				if ( !empty( $date ) ) {
					echo esc_html( $date );
				}

				break;

		}

	}

	add_action( 'manage_contact_posts_custom_column', 'baspa_contacts_type_columns_content', 10, 1 );

}

if ( !function_exists( 'baspa_contacts_type_admin_list' ) ) {

	/**
	 * Admin List
	 *
	 * @param WP_Query $wp_query
	 */
	function baspa_contacts_type_admin_list( WP_Query $wp_query ): void {

		if ( is_admin() ) {

			if ( $wp_query->query[ 'post_type' ] == 'contact' && !isset( $_GET[ 'order' ] ) ) {
				$wp_query->set( 'orderby', array(
					'date' => 'DESC',
				) );
			}

		}

	}

	add_filter( 'pre_get_posts', 'baspa_contacts_type_admin_list' );

}
