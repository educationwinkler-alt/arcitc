<?php

/**
 * Shared helpers for page section metabox data.
 */

if ( !function_exists( 'arctic_meta_text' ) ) {

	/**
	 * Read a plain text page/post meta value.
	 *
	 * @param int    $post_id
	 * @param string $key
	 * @param string $fallback
	 *
	 * @return string
	 */
	function arctic_meta_text( int $post_id, string $key, string $fallback = '' ): string {

		$value = get_post_meta( $post_id, $key, true );

		if ( is_array( $value ) ) {
			$value = '';
		}

		$value = trim( wp_strip_all_tags( (string) $value ) );

		return '' !== $value ? $value : $fallback;

	}

}

if ( !function_exists( 'arctic_meta_fieldset_rows' ) ) {

	/**
	 * Flatten Meta Box fieldset_text rows.
	 *
	 * @param int      $post_id
	 * @param string   $key
	 * @param string[] $row_keys
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function arctic_meta_fieldset_rows( int $post_id, string $key, array $row_keys = array() ): array {

		$rows = array();

		$is_row = static function ( array $candidate ) use ( $row_keys ): bool {

			if ( empty( $row_keys ) ) {
				return !empty( $candidate );
			}

			foreach ( $row_keys as $row_key ) {
				if ( array_key_exists( $row_key, $candidate ) ) {
					return true;
				}
			}

			return false;

		};

		$collect_rows = null;
		$collect_rows = static function ( $value ) use ( &$collect_rows, &$rows, $is_row ): void {

			if ( !is_array( $value ) ) {
				return;
			}

			if ( $is_row( $value ) ) {
				$rows[] = $value;

				return;
			}

			foreach ( $value as $nested_value ) {
				$collect_rows( $nested_value );
			}

		};

		foreach ( get_post_meta( $post_id, $key ) as $raw_row ) {
			$collect_rows( $raw_row );
		}

		return $rows;

	}

}

if ( !function_exists( 'arctic_meta_attachment_ids' ) ) {

	/**
	 * Read repeated attachment IDs from Meta Box image_advanced fields.
	 *
	 * @param int    $post_id
	 * @param string $key
	 *
	 * @return int[]
	 */
	function arctic_meta_attachment_ids( int $post_id, string $key ): array {

		$ids         = array();
		$collect_ids = null;
		$collect_ids = static function ( $value ) use ( &$collect_ids, &$ids ): void {

			if ( is_object( $value ) ) {
				$value = get_object_vars( $value );
			}

			if ( is_numeric( $value ) ) {
				$id = absint( $value );
				if ( $id > 0 ) {
					$ids[] = $id;
				}

				return;
			}

			if ( !is_array( $value ) ) {
				return;
			}

			foreach ( array( 'ID', 'id', 'attachment_id' ) as $id_key ) {
				if ( array_key_exists( $id_key, $value ) ) {
					$collect_ids( $value[ $id_key ] );

					return;
				}
			}

			foreach ( $value as $nested_value ) {
				$collect_ids( $nested_value );
			}

		};

		foreach ( get_post_meta( $post_id, $key ) as $raw_id ) {
			$collect_ids( $raw_id );
		}

		return array_values( array_unique( $ids ) );

	}

}
