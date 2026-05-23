<?php

/**
 * Blog Loop
 */

get_template_part( 'templates/loop', '', array(
	'query_module'     => 'posts',
	'query_args'       => array(
		'post_type'           => 'post',
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => false,
		'no_found_rows'       => false,
	),
	'query_class'      => array(
		'f-listings',
		'a-grid',
		'a-grid--cols-1',
		'a-gap--m',
	),
	'query_paged'      => true,
	'query_pagination' => true,
	'query_listing'    => 'list',
	)
);
