<?php

/**
 * Rewrite
 */

if ( !function_exists( 'baspa_products_rewrite_rules' ) ) {

	function baspa_products_rewrite_rules( $rules ): array {
		$new_rules = array();

		$taxonomy = get_taxonomy( 'product-category' );

		if ( $taxonomy && isset( $taxonomy->rewrite[ 'slug' ] ) ) {

			$taxonomy_slug = $taxonomy->rewrite[ 'slug' ]; // Získání slugu taxonomie

			$terms         = get_terms( [
				'taxonomy'   => 'product-category',
				'hide_empty' => false,
			] );

			if ( !is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_slug = $term->slug;

					// single term
					$new_rules[ $taxonomy_slug . '/' . $term_slug . '/?$' ] = 'index.php?product-category=' . $term_slug;

					// hierarchical term
					$ancestors = get_ancestors( $term->term_id, 'product-category' );
					if ( !empty( $ancestors ) ) {
						$hierarchical_slug = implode( '/', array_map( function ( $ancestor_id ) {
								$ancestor = get_term( $ancestor_id );

								return $ancestor->slug;
							}, array_reverse( $ancestors ) ) ) . '/' . $term_slug;

						$new_rules[ $taxonomy_slug . '/' . $hierarchical_slug . '/?$' ] = 'index.php?product-category=' . $term_slug;
					}
				}
			}
		}

		return $new_rules + $rules;

	}

	add_filter( 'rewrite_rules_array', 'baspa_products_rewrite_rules' );

}

//add_filter('post_type_link', 'custom_product_permalink', 10, 2);
//function custom_product_permalink($post_link, $post) {
//	if ($post->post_type !== 'product') {
//		return $post_link;
//	}
//
//	// Získejte všechny termy v hierarchické taxonomii
//	$terms = get_the_terms($post->ID, 'product-category');
//	if ($terms && !is_wp_error($terms)) {
//		// Najděte nejvýše postavený term
//		$main_term = null;
//		foreach ($terms as $term) {
//			if ($main_term === null || $term->parent === 0) {
//				$main_term = $term;
//			}
//		}
//
//		if ($main_term) {
//			// Sestavte hierarchický slug
//			$hierarchy = get_term_parents_list($main_term->term_id, 'product-category', [
//				'link' => false,
//				'format' => 'slug',
//				'separator' => '/'
//			]);
//
//			// Odstraňte případnou poslední lomítka a přidejte část pro post_name
//			return home_url(rtrim($hierarchy, '/') . "/produkt/{$post->post_name}/");
//		}
//	}
//
//	return $post_link;
//}
//
//
//add_action('init', function () {
//	add_rewrite_rule(
//		'^product-category/([^/]+)/([^/]+)/([^/]+)/?$',
//		'index.php?product-category=$matches[1]&product-category=$matches[2]&product=$matches[3]',
//		'top'
//	);
//	flush_rewrite_rules();
//});
