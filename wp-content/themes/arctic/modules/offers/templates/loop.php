<?php

/**
 * Loop
 */

get_template_part( 'templates/loop', '', array(
	'query_module'     => 'offers',
	'query_args'       => array(
		'post_type'           => 'offer',
		'orderby'   => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'ignore_sticky_posts' => false,
		'no_found_rows'       => false,
	),
	'query_paged'      => true,
	'query_pagination' => true,
) );
