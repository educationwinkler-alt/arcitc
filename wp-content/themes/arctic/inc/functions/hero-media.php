<?php

/**
 * Shared hero media helpers.
 */

if ( !function_exists( 'arctic_hero_media_type_options' ) ) {

	function arctic_hero_media_type_options(): array {

		return array(
			'image' => esc_html_x( 'Image', 'admin hero media', 'baspa' ),
			'video' => esc_html_x( 'Video', 'admin hero media', 'baspa' ),
		);

	}

}

if ( !function_exists( 'arctic_hero_media_clean_type' ) ) {

	function arctic_hero_media_clean_type( string $value ): string {

		$value = sanitize_key( $value );

		return 'video' === $value ? 'video' : 'image';

	}

}

if ( !function_exists( 'arctic_hero_media_first_attachment_id' ) ) {

	function arctic_hero_media_first_attachment_id( mixed $value ): int {

		if ( is_numeric( $value ) ) {
			return absint( $value );
		}

		if ( is_array( $value ) ) {
			if ( isset( $value['ID'] ) && is_numeric( $value['ID'] ) ) {
				return absint( $value['ID'] );
			}

			if ( isset( $value['id'] ) && is_numeric( $value['id'] ) ) {
				return absint( $value['id'] );
			}

			foreach ( $value as $nested_value ) {
				$attachment_id = arctic_hero_media_first_attachment_id( $nested_value );

				if ( $attachment_id > 0 ) {
					return $attachment_id;
				}
			}
		}

		return 0;

	}

}

if ( !function_exists( 'arctic_hero_post_meta_attachment_id' ) ) {

	function arctic_hero_post_meta_attachment_id( int $post_id, string $meta_key ): int {

		$attachment_id = arctic_hero_media_first_attachment_id( get_post_meta( $post_id, $meta_key, false ) );

		if ( $attachment_id > 0 ) {
			return $attachment_id;
		}

		return arctic_hero_media_first_attachment_id( get_post_meta( $post_id, $meta_key, true ) );

	}

}

if ( !function_exists( 'arctic_hero_term_meta_attachment_id' ) ) {

	function arctic_hero_term_meta_attachment_id( int $term_id, string $meta_key ): int {

		return arctic_hero_media_first_attachment_id( get_term_meta( $term_id, $meta_key, true ) );

	}

}

if ( !function_exists( 'arctic_hero_attachment_is_video' ) ) {

	function arctic_hero_attachment_is_video( int $attachment_id ): bool {

		$mime_type = (string) get_post_mime_type( $attachment_id );

		return str_starts_with( $mime_type, 'video/' );

	}

}

if ( !function_exists( 'arctic_hero_attachment_is_image' ) ) {

	function arctic_hero_attachment_is_image( int $attachment_id ): bool {

		return $attachment_id > 0 && wp_attachment_is_image( $attachment_id );

	}

}

if ( !function_exists( 'arctic_hero_term_prefix' ) ) {

	function arctic_hero_term_prefix( string $taxonomy ): string {

		return 'product-series' === $taxonomy ? 'series_heading' : 'category_heading';

	}

}

if ( !function_exists( 'arctic_hero_post_prefix' ) ) {

	function arctic_hero_post_prefix( int $post_id ): string {

		return match ( get_post_type( $post_id ) ) {
			'product' => 'product_hero',
			'slide'   => 'slide_hero',
			default   => 'page_hero',
		};

	}

}

if ( !function_exists( 'arctic_hero_image_data' ) ) {

	function arctic_hero_image_data( int $image_id, string $image_size = 'full' ): array {

		if ( !arctic_hero_attachment_is_image( $image_id ) ) {
			return array();
		}

		$image = wp_get_attachment_image_src( $image_id, $image_size );

		if ( empty( $image ) ) {
			return array();
		}

		$caption = wp_get_attachment_caption( $image_id );
		$alt     = trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );

		return array(
			'id'      => $image_id,
			'url'     => (string) $image[0],
			'width'   => (int) $image[1],
			'height'  => (int) $image[2],
			'alt'     => $alt,
			'caption' => $caption ? (string) $caption : '',
			'size'    => $image_size,
		);

	}

}

if ( !function_exists( 'arctic_hero_media_from_post' ) ) {

	function arctic_hero_media_from_post( int $post_id, ?string $prefix = null, int $image_id = 0, array $args = array() ): array {

		$prefix     = $prefix ?: arctic_hero_post_prefix( $post_id );
		$image_size = (string) ( $args['image_size'] ?? get_template() . '-huge' );
		$source     = (string) ( $args['source'] ?? $prefix );
		$type       = arctic_hero_media_clean_type( (string) get_post_meta( $post_id, $prefix . '_media_type', true ) );
		$video_id   = arctic_hero_post_meta_attachment_id( $post_id, $prefix . '_video' );

		if ( 'video' === $type && $video_id > 0 && arctic_hero_attachment_is_video( $video_id ) ) {
			$poster_id = arctic_hero_post_meta_attachment_id( $post_id, $prefix . '_poster_image' );
			$poster_id = $poster_id > 0 ? $poster_id : $image_id;
			$poster    = arctic_hero_image_data( $poster_id, $image_size );

			return array(
				'type'        => 'video',
				'source'      => $source,
				'video_id'    => $video_id,
				'video_url'   => (string) wp_get_attachment_url( $video_id ),
				'video_mime'  => (string) get_post_mime_type( $video_id ),
				'poster'      => $poster,
				'caption'     => $poster['caption'] ?? '',
				'asset_status' => 'admin-hero-video',
			);
		}

		if ( arctic_hero_attachment_is_image( $image_id ) ) {
			return array(
				'type'         => 'image',
				'source'       => $source,
				'image'        => arctic_hero_image_data( $image_id, $image_size ),
				'caption'      => wp_get_attachment_caption( $image_id ) ?: '',
				'asset_status' => 'admin-hero-image',
			);
		}

		return array( 'type' => 'none' );

	}

}

if ( !function_exists( 'arctic_hero_media_from_term' ) ) {

	function arctic_hero_media_from_term( int $term_id, string $taxonomy, array $args = array() ): array {

		$prefix       = arctic_hero_term_prefix( $taxonomy );
		$image_size   = (string) ( $args['image_size'] ?? get_template() . '-huge' );
		$fallback_key = (string) ( $args['fallback_image_key'] ?? ( 'product-category' === $taxonomy ? 'category_image' : '' ) );
		$type         = arctic_hero_media_clean_type( (string) get_term_meta( $term_id, $prefix . '_media_type', true ) );
		$image_id     = arctic_hero_term_meta_attachment_id( $term_id, $prefix . '_image' );
		$image_id     = $image_id > 0 ? $image_id : ( '' !== $fallback_key ? arctic_hero_term_meta_attachment_id( $term_id, $fallback_key ) : 0 );
		$video_id     = arctic_hero_term_meta_attachment_id( $term_id, $prefix . '_video' );

		if ( 'video' === $type && $video_id > 0 && arctic_hero_attachment_is_video( $video_id ) ) {
			$poster_id = arctic_hero_term_meta_attachment_id( $term_id, $prefix . '_poster_image' );
			$poster_id = $poster_id > 0 ? $poster_id : $image_id;
			$poster    = arctic_hero_image_data( $poster_id, $image_size );

			return array(
				'type'         => 'video',
				'source'       => 'term-hero',
				'video_id'     => $video_id,
				'video_url'    => (string) wp_get_attachment_url( $video_id ),
				'video_mime'   => (string) get_post_mime_type( $video_id ),
				'poster'       => $poster,
				'caption'      => $poster['caption'] ?? '',
				'asset_status' => 'admin-term-hero-video',
			);
		}

		if ( arctic_hero_attachment_is_image( $image_id ) ) {
			return array(
				'type'         => 'image',
				'source'       => 'term-hero',
				'image'        => arctic_hero_image_data( $image_id, $image_size ),
				'caption'      => wp_get_attachment_caption( $image_id ) ?: '',
				'asset_status' => 'admin-term-hero-image',
			);
		}

		return array( 'type' => 'none' );

	}

}

if ( !function_exists( 'arctic_post_has_hero_media' ) ) {

	function arctic_post_has_hero_media( int $post_id, ?string $prefix = null, int $image_id = 0 ): bool {

		$media = arctic_hero_media_from_post( $post_id, $prefix, $image_id );

		return !empty( $media['type'] ) && 'none' !== $media['type'];

	}

}

if ( !function_exists( 'arctic_post_has_hero_video' ) ) {

	function arctic_post_has_hero_video( int $post_id, ?string $prefix = null ): bool {

		$prefix   = $prefix ?: arctic_hero_post_prefix( $post_id );
		$type     = arctic_hero_media_clean_type( (string) get_post_meta( $post_id, $prefix . '_media_type', true ) );
		$video_id = arctic_hero_post_meta_attachment_id( $post_id, $prefix . '_video' );

		return 'video' === $type && $video_id > 0 && arctic_hero_attachment_is_video( $video_id );

	}

}

if ( !function_exists( 'arctic_term_has_hero_media' ) ) {

	function arctic_term_has_hero_media( int $term_id, string $taxonomy ): bool {

		$media = arctic_hero_media_from_term( $term_id, $taxonomy );

		return !empty( $media['type'] ) && 'none' !== $media['type'];

	}

}

if ( !function_exists( 'arctic_render_hero_media' ) ) {

	function arctic_render_hero_media( array $media, array $classes, array $attrs = array() ): void {

		$type = (string) ( $media['type'] ?? 'none' );

		if ( 'none' === $type ) {
			return;
		}

		$classes[] = 'f-hero-media';
		$classes[] = 'f-hero-media--' . $type;

		$attrs['class']               = implode( ' ', array_filter( array_map( 'sanitize_html_class', $classes ) ) );
		$attrs['data-hero-media']     = $type;
		$attrs['data-content-source'] = (string) ( $media['source'] ?? 'hero-media' );
		$attrs['data-asset-status']   = (string) ( $media['asset_status'] ?? ( 'video' === $type ? 'admin-hero-video' : 'admin-hero-image' ) );

		$attr_html = '';
		foreach ( $attrs as $name => $value ) {
			if ( '' === (string) $value ) {
				continue;
			}
			$attr_html .= ' ' . esc_attr( (string) $name ) . '="' . esc_attr( (string) $value ) . '"';
		}
		?>
		<figure<?php echo $attr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( 'video' === $type ) {
				$poster = $media['poster'] ?? array();
				if ( !empty( $poster['id'] ) ) {
					echo wp_get_attachment_image( (int) $poster['id'], (string) ( $poster['size'] ?? 'full' ), false, array(
						'class'       => 'f-hero-media__poster',
						'alt'         => '',
						'aria-hidden' => 'true',
						'loading'     => 'eager',
						'decoding'    => 'async',
					) );
				}
				?>
				<video class="f-hero-media__video"
				       autoplay
				       muted
				       loop
				       playsinline
				       preload="metadata"
				       <?php if ( !empty( $poster['url'] ) ) { ?>
					       poster="<?php echo esc_url( (string) $poster['url'] ); ?>"
				       <?php } ?>
				       aria-hidden="true">
					<source src="<?php echo esc_url( (string) ( $media['video_url'] ?? '' ) ); ?>" type="<?php echo esc_attr( (string) ( $media['video_mime'] ?? 'video/mp4' ) ); ?>">
				</video>
			<?php } elseif ( !empty( $media['image']['id'] ) ) {
				echo wp_get_attachment_image( (int) $media['image']['id'], (string) ( $media['image']['size'] ?? 'full' ), false, array(
					'alt'           => (string) ( $media['image']['alt'] ?? '' ),
					'fetchpriority' => 'high',
					'loading'       => 'eager',
					'decoding'      => 'async',
				) );
			} ?>

			<?php if ( !empty( $media['caption'] ) ) { ?>
				<figcaption class="f-background__caption">
					<?php echo esc_html( (string) $media['caption'] ); ?>
				</figcaption>
			<?php } ?>
		</figure>
		<?php

	}

}
