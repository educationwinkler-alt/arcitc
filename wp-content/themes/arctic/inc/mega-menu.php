<?php

/**
 * Mega Menu
 */

if ( !function_exists( 'arctic_mega_menu_cache_key' ) ) {
	function arctic_mega_menu_cache_key(): string {
		return 'arctic_mega_menu_v3';
	}
}

if ( !function_exists( 'arctic_mega_menu_definitions' ) ) {
	/**
	 * Build deterministic menu definitions that can be overridden via filter.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function arctic_mega_menu_definitions(): array {
		$definitions = array(
			array(
				'key'       => 'hot-tubs',
				'label'     => __( 'Vířivky', 'baspa' ),
				'url'       => home_url( '/virivky/' ),
				'page_path' => 'virivky',
				'columns'   => 3,
			),
			array(
				'key'       => 'swimspa',
				'label'     => __( 'Celoroční bazény', 'baspa' ),
				'url'       => home_url( '/swimspa/' ),
				'page_path' => 'swimspa',
				'columns'   => 3,
			),
		);

		/** This filter allows IA changes without hardcoding taxonomy slugs in the template. */
		return apply_filters( 'arctic_mega_menu_definitions', $definitions );
	}
}

if ( !function_exists( 'arctic_mega_menu_resolve_category_term' ) ) {
	function arctic_mega_menu_resolve_category_term( array $definition ): ?WP_Term {
		$page_path = trim( (string) ( $definition['page_path'] ?? '' ) );
		if ( $page_path !== '' ) {
			$page = get_page_by_path( $page_path, OBJECT, 'page' );
			if ( $page instanceof WP_Post ) {
				$category_id = (int) get_post_meta( $page->ID, 'page_product_category', true );
				if ( $category_id > 0 ) {
					$term = get_term( $category_id, 'product-category' );
					if ( $term instanceof WP_Term && !is_wp_error( $term ) ) {
						return $term;
					}
				}
			}
		}

		$menu_url = (string) ( $definition['url'] ?? '' );
		$menu_path = trim( (string) wp_parse_url( $menu_url, PHP_URL_PATH ), '/' );
		if ( $menu_path !== '' ) {
			$slug = sanitize_title( basename( $menu_path ) );
			if ( $slug !== '' ) {
				$fallback = get_term_by( 'slug', $slug, 'product-category' );
				if ( $fallback instanceof WP_Term ) {
					return $fallback;
				}
			}
		}

		return null;
	}
}

if ( !function_exists( 'arctic_mega_menu_query_products' ) ) {
	/**
	 * @return array<int, WP_Post>
	 */
	function arctic_mega_menu_query_products( int $category_id ): array {
		if ( $category_id <= 0 ) {
			return array();
		}

		$posts = get_posts( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'tax_query'      => array(
				array(
					'taxonomy'         => 'product-category',
					'field'            => 'term_id',
					'terms'            => $category_id,
					'include_children' => true,
				),
			),
		) );

		return is_array( $posts ) ? $posts : array();
	}
}

if ( !function_exists( 'arctic_mega_menu_products_by_series' ) ) {
	/**
	 * @param array<int, WP_Post> $products
	 *
	 * @return array<int, array{term: ?WP_Term, products: array<int, WP_Post>, count: int}>
	 */
	function arctic_mega_menu_products_by_series( array $products ): array {
		$groups = array();
		$plain = array();

		foreach ( $products as $product ) {
			if ( !( $product instanceof WP_Post ) ) {
				continue;
			}

			$series = get_the_terms( $product->ID, 'product-series' );
			if ( empty( $series ) || is_wp_error( $series ) ) {
				$plain[] = $product;
				continue;
			}

			usort( $series, static function ( WP_Term $a, WP_Term $b ): int {
				return strcmp( $a->name, $b->name );
			} );

			$term = $series[0];
			$term_id = (int) $term->term_id;

			if ( !isset( $groups[ $term_id ] ) ) {
				$groups[ $term_id ] = array(
					'term'     => $term,
					'products' => array(),
					'count'    => 0,
				);
			}

			$groups[ $term_id ]['products'][] = $product;
			$groups[ $term_id ]['count']++;
		}

		$grouped = array_values( $groups );
		usort( $grouped, static function ( array $left, array $right ): int {
			$series_order = static function ( ?WP_Term $term ): int {
				if ( !( $term instanceof WP_Term ) ) {
					return 100;
				}

				$key = strtolower( sanitize_title( $term->slug ?: $term->name ) );
				$order = array(
					'core'    => 10,
					'classic' => 20,
					'custom'  => 30,
				);

				return $order[ $key ] ?? 100;
			};

			$left_order = $series_order( $left['term'] ?? null );
			$right_order = $series_order( $right['term'] ?? null );

			if ( $left_order !== $right_order ) {
				return $left_order <=> $right_order;
			}

			if ( $left['count'] !== $right['count'] ) {
				return $right['count'] <=> $left['count'];
			}

			$left_name = $left['term'] instanceof WP_Term ? $left['term']->name : '';
			$right_name = $right['term'] instanceof WP_Term ? $right['term']->name : '';
			return strcmp( $left_name, $right_name );
		} );

		if ( !empty( $plain ) ) {
			$grouped[] = array(
				'term'     => null,
				'products' => $plain,
				'count'    => count( $plain ),
			);
		}

		return $grouped;
	}
}

if ( !function_exists( 'arctic_mega_menu_label_from_term' ) ) {
	function arctic_mega_menu_label_from_term( ?WP_Term $term, string $fallback, int $index ): string {
		if ( $term instanceof WP_Term ) {
			$name = trim( $term->name );
			if ( $name !== '' ) {
				return sprintf( __( 'Série %s', 'baspa' ), $name );
			}
		}

		return sprintf( '%s %d', $fallback, $index + 1 );
	}
}

if ( !function_exists( 'arctic_mega_menu_build_columns' ) ) {
	/**
	 * @param array<int, WP_Post> $products
	 *
	 * @return array<int, array{label: string, products: array<int, WP_Post>}>
	 */
	function arctic_mega_menu_build_columns( array $products, string $fallback_label, int $columns = 3 ): array {
		$columns = max( 1, (int) $columns );
		$groups = arctic_mega_menu_products_by_series( $products );
		$result = array();

		$index = 0;
		while ( $index < $columns ) {
			$current = $groups[ $index ] ?? null;
			$result[] = array(
				'label'    => arctic_mega_menu_label_from_term(
					$current['term'] ?? null,
					$fallback_label,
					$index
				),
				'products' => $current['products'] ?? array(),
			);
			$index++;
		}

		$empty_columns = array_filter( $result, static function ( array $column ): bool {
			return empty( $column['products'] );
		} );

		// If sparse series data would create empty contract columns, rebalance by product chunks.
		if ( !empty( $empty_columns ) && count( $products ) >= $columns ) {
			$chunks = array_chunk( $products, max( 1, (int) ceil( count( $products ) / $columns ) ) );

			$index = 0;
			while ( $index < $columns ) {
				$current = $groups[ $index ] ?? null;
				$result[ $index ] = array(
					'label'    => arctic_mega_menu_label_from_term(
						$current['term'] ?? null,
						$fallback_label,
						$index
					),
					'products' => $chunks[ $index ] ?? array(),
				);
				$index++;
			}
		}

		return $result;
	}
}

if ( !function_exists( 'arctic_mega_menu_format_title' ) ) {
	function arctic_mega_menu_format_title( string $title ): string {
		$normalized = trim( $title );
		$prefixes = array(
			'Vířivka ',
			'Virivka ',
			'Celoroční bazén ',
			'Celorocni bazen ',
		);

		foreach ( $prefixes as $prefix ) {
			if ( str_starts_with( $normalized, $prefix ) ) {
				return trim( substr( $normalized, strlen( $prefix ) ) );
			}
		}

		return $normalized;
	}
}
if ( !function_exists( 'arctic_get_desktop_mega_menus' ) ) {
	/**
	 * @return array<int, array<string, mixed>>
	 */
	function arctic_get_desktop_mega_menus(): array {
		$cached = get_transient( arctic_mega_menu_cache_key() );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$menus = array();
		foreach ( arctic_mega_menu_definitions() as $definition ) {
			$category = arctic_mega_menu_resolve_category_term( $definition );
			$category_id = $category instanceof WP_Term ? (int) $category->term_id : 0;

			$products = arctic_mega_menu_query_products( $category_id );
			$columns = arctic_mega_menu_build_columns(
				$products,
				__( 'Produkty', 'baspa' ),
				(int) ( $definition['columns'] ?? 3 )
			);

			$menus[] = array(
				'key'          => (string) ( $definition['key'] ?? '' ),
				'label'        => (string) ( $definition['label'] ?? '' ),
				'url'          => (string) ( $definition['url'] ?? home_url( '/' ) ),
				'columns'      => $columns,
				'product_count'=> count( $products ),
			);
		}

		set_transient( arctic_mega_menu_cache_key(), $menus, 12 * HOUR_IN_SECONDS );

		return $menus;
	}
}

if ( !function_exists( 'arctic_flush_mega_menu_cache' ) ) {
	function arctic_flush_mega_menu_cache(): void {
		delete_transient( arctic_mega_menu_cache_key() );
	}
}

if ( !function_exists( 'arctic_flush_mega_menu_cache_for_product' ) ) {
	function arctic_flush_mega_menu_cache_for_product( int $post_id ): void {
		if ( get_post_type( $post_id ) === 'product' ) {
			arctic_flush_mega_menu_cache();
		}
	}
}

if ( !function_exists( 'arctic_flush_mega_menu_cache_for_set_terms' ) ) {
	function arctic_flush_mega_menu_cache_for_set_terms(
		int $object_id,
		$terms,
		array $tt_ids,
		string $taxonomy,
		bool $append,
		array $old_tt_ids
	): void {
		unset( $terms, $tt_ids, $append, $old_tt_ids );

		if ( !in_array( $taxonomy, array( 'product-category', 'product-series' ), true ) ) {
			return;
		}

		if ( get_post_type( $object_id ) === 'product' ) {
			arctic_flush_mega_menu_cache();
		}
	}
}

if ( !function_exists( 'arctic_flush_mega_menu_cache_for_term' ) ) {
	function arctic_flush_mega_menu_cache_for_term(
		int $term_id,
		int $tt_id,
		string $taxonomy
	): void {
		unset( $term_id, $tt_id );
		if ( in_array( $taxonomy, array( 'product-category', 'product-series' ), true ) ) {
			arctic_flush_mega_menu_cache();
		}
	}
}

add_action( 'save_post_product', 'arctic_flush_mega_menu_cache_for_product', 10, 1 );
add_action( 'trashed_post', 'arctic_flush_mega_menu_cache_for_product', 10, 1 );
add_action( 'untrashed_post', 'arctic_flush_mega_menu_cache_for_product', 10, 1 );
add_action( 'deleted_post', 'arctic_flush_mega_menu_cache_for_product', 10, 1 );
add_action( 'set_object_terms', 'arctic_flush_mega_menu_cache_for_set_terms', 10, 6 );
add_action( 'created_term', 'arctic_flush_mega_menu_cache_for_term', 10, 3 );
add_action( 'edited_term', 'arctic_flush_mega_menu_cache_for_term', 10, 3 );
add_action( 'delete_term', 'arctic_flush_mega_menu_cache_for_term', 10, 3 );


