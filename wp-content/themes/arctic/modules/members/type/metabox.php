<?php

/**
 * Metabox
 */

if ( !function_exists( 'baspa_members_metabox_register' ) ) {

	/**
	 * Register Metabox
	 *
	 * @param array $meta_boxes
	 *
	 * @return array
	 */
	function baspa_members_metabox_register( array $meta_boxes ): array {

		// Metabox
		$meta_boxes[] = array(
			'id'         => 'baspa-metabox--member',
			'title'      => esc_html__( 'Member', 'baspa' ),
			'post_types' => array( 'member' ),
			'priority'   => 'high',
			'fields'     => array(
				array(
					'name'    => esc_html__( 'Display on Contact Page?', 'baspa' ),
					'id'      => 'member_contacts',
					'type'    => 'checkbox',
				),
				array(
					'name'             => esc_html__( 'Contact avatar', 'baspa' ),
					'id'               => 'member_avatar',
					'type'             => 'image_advanced',
					'max_file_uploads' => 1,
					'desc'             => esc_html__( 'Square headshot used in small round contact cards. Featured image remains the larger team portrait.', 'baspa' ),
				),
				array(
					'name'    => esc_html__( 'Position', 'baspa' ),
					'id'      => 'member_position',
					'type'    => 'text',
				),
				array(
					'name'    => esc_html__( 'Scope', 'baspa' ),
					'id'      => 'member_scope',
					'type'    => 'text',
				),
				array(
					'name'    => esc_html__( 'Email', 'baspa' ),
					'id'      => 'member_email',
					'type'    => 'email',
				),
				array(
					'name'    => esc_html__( 'Phone', 'baspa' ),
					'id'      => 'member_phone',
					'type'    => 'text',
				),
			),
		);

		return $meta_boxes;

	}

	add_filter( 'rwmb_meta_boxes', 'baspa_members_metabox_register' );

}
