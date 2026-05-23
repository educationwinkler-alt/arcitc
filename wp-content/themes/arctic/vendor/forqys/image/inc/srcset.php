<?php

/**
 * SRCSET Functions
 *
 * @since 1.0.8
 */

if ( !function_exists( 'forqy_image_get_srcset' ) ) {

	/**
	 * Get Image SRCSET
	 *
	 * @param $thumbnail_id
	 * @param $size
	 *
	 * @return string
	 */
	function forqy_image_get_srcset( $thumbnail_id, $size ): string {
		$thumbnail_srcset = wp_get_attachment_image_srcset( $thumbnail_id, $size );

		if ( $thumbnail_srcset ) {
			$new_srcset = array();
			$entries    = explode( ',', $thumbnail_srcset );

			foreach ( $entries as $entry ) {
				$parts      = preg_split( '/\s+/', trim( $entry ) );
				$url        = $parts[ 0 ];
				$descriptor = $parts[ 1 ] ?? '';

				// Convert to WebP if exists
				$webp_url      = esc_url( preg_replace( '/\.(jpe?g|png)$/i', '.webp', $url ) ); // without extension IMAGE.webp
				$webp_url_ext  = esc_url( $url . '.webp' ); // with extension IMAGE.png.webp
				$webp_path     = str_replace( trailingslashit( esc_url( home_url() ) ), ABSPATH, $webp_url );
				$webp_path_ext = str_replace( trailingslashit( esc_url( home_url() ) ), ABSPATH, $webp_url_ext );

				if ( file_exists( $webp_path ) ) {
					$new_srcset[] = $webp_url . ( $descriptor ? ' ' . $descriptor : '' );
				} else if ( file_exists( $webp_path_ext ) ) {
					$new_srcset[] = $webp_url_ext . ( $descriptor ? ' ' . $descriptor : '' );
				}
			}

			if ( !empty( $new_srcset ) ) {
				$thumbnail_srcset = implode( ', ', $new_srcset );
			}
		}

		return $thumbnail_srcset;
	}
}
