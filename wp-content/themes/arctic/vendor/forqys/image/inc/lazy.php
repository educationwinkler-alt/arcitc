<?php

/**
 * Image Lazy
 *
 * @package forqy/image
 * @since   1.0.0
 *
 * To disable the lazy load of images:
 * add_filter( 'forqy_image_lazy', '__return_false' );
 * add_filter( 'forqy_image_lazy_content', '__return_false' );
 */

/*
FILTERS

Disable image lazy loading
add_filter( 'forqy_image_lazy', '__return_false' );

Disable content image lazy loading
add_filter( 'forqy_image_lazy_content', '__return_false' );

Enable content image lazy loading for the first image - Disabled for homepage by default!
add_filter( 'forqy_image_lazy_content_first_disabled', '__return_false' );
*/

if ( !function_exists( 'forqy_lazy_scripts' ) ) {

	/**
	 * Scripts
	 *
	 * @return void
	 */
	function forqy_lazy_scripts(): void {

		if ( !wp_script_is( get_template() . '-lazy', 'enqueued' ) && ( apply_filters( 'forqy_image_lazy', true ) || apply_filters( 'forqy_image_lazy_content', true ) ) ) {

			/**
			 * LazyLoad
			 *
			 * @url https://github.com/verlok/vanilla-lazyload
			 * @source https://github.com/verlok/vanilla-lazyload/blob/master/dist/lazyload.js
			 */
			wp_enqueue_script(
				'lazyload',
				str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/js/lib/lazyload.js' ),
				false,
				'19.1.3',
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				),
			);

			/**
			 * Lazy
			 */
			wp_enqueue_script(
				get_template() . '-lazy',
				str_replace( get_template_directory(), get_template_directory_uri(), dirname( __DIR__ ) . '/js/lazy.js' ),
				array(
					'lazyload',
				),
				'1.0.0',
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				),
			);

		}

	}

	add_action( 'wp_enqueue_scripts', 'forqy_lazy_scripts', 5 );

}

if ( !function_exists( 'forqy_lazy_body_class' ) ) {

	function forqy_lazy_body_class( $classes ) {

		if ( apply_filters( 'forqy_image_lazy', true ) ) {
			$classes[] = 'image--lazy';
		}
		if ( apply_filters( 'forqy_image_lazy_content', true ) ) {
			$classes[] = 'image-content--lazy';
		}

		return $classes;
	}

	add_filter( 'body_class', 'forqy_lazy_body_class' );

}

if ( !function_exists( 'forqy_lazy_native' ) ) {

	/**
	 * Disabling Completely Native Lazy Load Due to Safari 15.4+ Bug
	 *
	 * @url https://github.com/verlok/vanilla-lazyload/issues/563#issuecomment-1094073559
	 * @url https://pepelsbey.dev/articles/lazy-loading-safari/#full-body
	 * @url https://bugs.webkit.org/show_bug.cgi?id=39455
	 *
	 * @return bool
	 */
	function forqy_lazy_native(): bool {

		if ( !is_admin() && apply_filters( 'forqy_image_lazy', true ) ) {
			return false;
		}

		return true;

	}

//	add_filter( 'wp_lazy_loading_enabled', 'forqy_lazy_native' );

}

if ( !function_exists( 'forqy_lazy_native_image' ) ) {

	/**
	 * Disabling Native Lazy Load for Images Due to Safari 15.4+ Bug
	 *
	 * @url https://github.com/verlok/vanilla-lazyload/issues/563#issuecomment-1094073559
	 * @url https://pepelsbey.dev/articles/lazy-loading-safari/#full-body
	 * @url https://bugs.webkit.org/show_bug.cgi?id=39455
	 *
	 * @param $default
	 * @param $tag_name
	 * @param $context
	 *
	 * @return false|mixed
	 */
	function forqy_lazy_native_image( $default, $tag_name, $context ) {

		if ( !is_admin() && apply_filters( 'forqy_image_lazy', true ) ) {

			// Disable native lazy load for images only
			if ( 'img' === $tag_name ) {
				return false;
			}
		}

		return $default;
	}

//	add_filter( 'wp_lazy_loading_enabled', 'forqy_lazy_native_image', 10, 3 );

}

if ( !function_exists( 'forqy_lazy_native_image_attributes' ) ) {

	/**
	 * Disabling Native Lazy Load of Attachment
	 *
	 * @url https://github.com/verlok/vanilla-lazyload/issues/563#issuecomment-1094073559
	 * @url https://pepelsbey.dev/articles/lazy-loading-safari/#full-body
	 * @url https://bugs.webkit.org/show_bug.cgi?id=39455
	 *
	 * @param array $attr
	 * @param $attachment
	 *
	 * @return array
	 */
	function forqy_lazy_native_image_attributes( array $attr, $attachment = null ): array {

		if ( !is_admin() && apply_filters( 'forqy_image_lazy', true ) ) {
			unset( $attr[ 'loading' ] );
		}

		return $attr;

	}

//	add_filter( 'wp_get_attachment_image_attributes', 'forqy_lazy_native_image_attributes', 10, 2 );

}

if ( !function_exists( 'forqy_lazy_image_attributes' ) ) {

	/**
	 * Image Lazy Load 'data-src', 'data-srcset', 'data-sizes' on Attachment Images
	 *
	 * @param $attr
	 * @param $attachment
	 * @param $size
	 *
	 * @return mixed
	 */
	function forqy_lazy_image_attributes( $attr, $attachment, $size ) {

		if ( !is_admin() && apply_filters( 'forqy_image_lazy', true ) ) {

			// options
			$options = array_replace( array(
				'prefix' => 'fy',
			), apply_filters( 'forqy_theme', array() ) );

			// image meta
			$image_meta = !empty( $attachment ) ? wp_get_attachment_metadata( $attachment->ID ) : array();
			// image src
			$image_src = $attr[ 'src' ];
			// image dimensions
			$image_width  = $image_meta[ 'sizes' ][ $size ][ 'width' ] ?? ( $image_meta[ 'width' ] ?? ( $attr[ 'width' ] ?? null ) );
			$image_height = $image_meta[ 'sizes' ][ $size ][ 'height' ] ?? ( $image_meta[ 'height' ] ?? ( $attr[ 'height' ] ?? null ) );

			// data - possibility to disable lazy by attribute or query variable
			$data_lazy = !isset( $attr[ 'data-lazy' ] ) || filter_var( $attr[ 'data-lazy' ], FILTER_VALIDATE_BOOLEAN );
			$var_lazy  = filter_var( get_query_var( 'forqy_image_lazy', true ), FILTER_VALIDATE_BOOLEAN );

			// image attributes
			if ( $var_lazy && $data_lazy ) {

				// slide image
				if ( ( post_type_exists( 'slide' ) && get_post_type() == 'slide' ) || get_query_var( 'forqy_slide_image' ) ) {

					$slider_type = get_query_var( 'forqy_slider_type', 'swiper' );

					// if is the 'slide' post type
					if ( $slider_type == 'splide' ) {
						/**
						 * Splide
						 */
						if ( !empty( $image_width ) && !empty( $image_height ) ) {
							$attr[ 'src' ] = forqy_image_placeholder( $image_width, $image_height );
						} else {
							$attr[ 'src' ] = forqy_image_placeholder();
						}
						$attr[ 'data-splide-lazy' ] = $image_src;
					} else if ( $slider_type == 'swiper' ) {
						/**
						 * Swiper
						 */
						if ( empty( $attr[ 'loading' ] ) ) {
							$attr[ 'loading' ] = 'lazy';
						}
					}

					// preserve high-quality image
					if ( isset( $attr[ 'srcset' ] ) ) {
						unset( $attr[ 'srcset' ] );
					}
					if ( isset( $attr[ 'sizes' ] ) ) {
						unset( $attr[ 'sizes' ] );
					}

				} else {
					// classic image

					$attr[ 'class' ] = $options[ 'prefix' ] . '-lazy js-lazy' . ' ' . $attr[ 'class' ];

					// change 'src' to 'data-src'
					$attr[ 'data-src' ] = $image_src;

					// add 'src' as placeholder
					if ( !empty( $image_width ) && !empty( $image_height ) ) {
						$attr[ 'src' ] = forqy_image_placeholder( $image_width, $image_height );
					} else {
						$attr[ 'src' ] = forqy_image_placeholder();
					}

					// 'srcset' to 'data-srcset'
					if ( isset( $attr[ 'srcset' ] ) ) {
						$srcset                = $attr[ 'srcset' ];
						$attr[ 'data-srcset' ] = $srcset;
						unset( $attr[ 'srcset' ] );
					}

					// 'sizes' to 'data-sizes'
					if ( isset( $attr[ 'sizes' ] ) ) {
						$sizes                = $attr[ 'sizes' ];
						$attr[ 'data-sizes' ] = $sizes;
						unset( $attr[ 'sizes' ] );
					}
				}

				/**
				 * WooCommerce
				 */
				if ( function_exists( 'forqy_woocommerce_activated' ) && forqy_woocommerce_activated() ) {

					// preserve original image for woocommerce checkout
					if ( is_cart() || is_checkout() ) {
						$attr[ 'src' ]   = $image_src;
						$attr[ 'class' ] = $options[ 'prefix' ] . '-image';
						unset( $attr[ 'data-src' ] );
						unset( $attr[ 'data-srcset' ] );
						unset( $attr[ 'data-sizes' ] );
					}

					// add 'wp-post-image' class to woocommerce product image to work with variable product switching
					if ( is_product() ) {
						$attr[ 'class' ] = $attr[ 'class' ] . ' wp-post-image';
					}

				}

			}

		}

		return $attr;

	}

	add_filter( 'wp_get_attachment_image_attributes', 'forqy_lazy_image_attributes', 100, 3 );

}

if ( !function_exists( 'forqy_lazy_image_attributes_content_add_position' ) ) {

	/**
	 * Add Data Attribute 'data-image-position' to the First Image to Recognize the Position in Next Filters
	 *
	 * @param $content
	 *
	 * @return array|mixed|string|string[]
	 */
	function forqy_lazy_image_attributes_content_add_position( $content ) {
		$img          = '<img'; // string to find
		$img_position = strpos( $content, $img );
		$img_add      = '<img data-image-position="1"'; // string to add

		if ( !is_admin() && !( is_home() || is_front_page() ) ) { // avoid on homepage

			if ( $img_position !== false || apply_filters( 'forqy_image_lazy_content_first_disabled', true ) ) {
				return substr_replace( $content, $img_add, $img_position, strlen( $img ) );
			}
		}

		return $content;
	}

//	add_filter( 'the_content', 'forqy_lazy_image_attributes_content_add_position', 10 ); // TODO Improve [Issue with 'wp-block-heading'?]

}

if ( !function_exists( 'forqy_lazy_image_attributes_content' ) ) {

	/**
	 * Content Image Lazy Load
	 *
	 * @param $content
	 *
	 * @return string|string[]|null
	 */
	function forqy_lazy_image_attributes_content( $content ) {

		if ( !is_admin() && apply_filters( 'forqy_image_lazy_content', true ) ) {
			return preg_replace_callback( '/<(img)([^>]+?)(>(.*?)<\/\\1>|[\/]?>)/si', 'forqy_lazy_image_attributes_content__callback', $content );
		} else {
			return $content;
		}

	}

	add_filter( 'the_content', 'forqy_lazy_image_attributes_content', 20 );
	// callback for image blocks
	add_filter( 'render_block', function ( $block_content, $block ) {
		if ( $block[ 'blockName' ] === 'core/image' || $block[ 'blockName' ] === 'core/cover' || $block[ 'blockName' ] === 'core/media-text' ) {
			return forqy_lazy_image_attributes_content( $block_content );
		} else {
			return $block_content;
		}
	}, 30, 2 );

}

if ( !function_exists( 'forqy_lazy_image_attributes_content__callback' ) ) {

	/**
	 * Content Image Lazy Load - Callback
	 *
	 * @param $matches
	 *
	 * @return mixed|string
	 */
	function forqy_lazy_image_attributes_content__callback( $matches ) {

		// options
		$options = array_replace( array(
			'prefix' => 'fy',
		), apply_filters( 'forqy_theme', array() ) );

		// $matches[0] = original
		// $matches[1] = tag
		// $matches[2] = attributes
		// $matches[3] = >

		// original attributes
		$attributes_kses_hair = wp_kses_hair( $matches[ 2 ], wp_allowed_protocols() );

		// if missing 'src' attribute, return
		if ( empty( $attributes_kses_hair[ 'src' ] ) ) {
			return $matches[ 0 ];
		}
		// if already image with lazy loading, return
		if ( !empty( $attributes_kses_hair[ 'data-src' ] ) ) {
			return $matches[ 0 ];
		}
		// if woocommerce cart or checkout, return
		if ( function_exists( 'forqy_woocommerce_activated' ) && forqy_woocommerce_activated() ) {
			if ( is_cart() || is_checkout() ) {
				return $matches[ 0 ];
			}
		}

		// attributes
		$attributes = array();

		foreach ( $attributes_kses_hair as $name => $attribute ) {
			$attributes[ $name ] = $attribute[ 'value' ];
		}

		if ( !empty( $attributes[ 'data-image-position' ] ) == '1' ) {

			/**
			 * If is the First Image in 'the_content' - Do Not Load Lazy
			 */
			$class = $options[ 'prefix' ] . '-content__image';
			if ( isset( $attributes[ 'class' ] ) ) {
				$attributes[ 'class' ] .= ' ' . $class;
			} else {
				$attributes[ 'class' ] = $class;
			}
			// 'fetchpriority'
			if ( empty( $attributes[ 'fetchpriority' ] ) ) {
				$attributes[ 'fetchpriority' ] = 'high';
			}

		} else {

			/**
			 * If is Another Image to Load Lazy
			 */
			$class = $options[ 'prefix' ] . '-content__image ' . $options[ 'prefix' ] . '-lazy js-lazy';
			if ( isset( $attributes[ 'class' ] ) ) {
				$attributes[ 'class' ] .= ' ' . $class;
			} else {
				$attributes[ 'class' ] = $class;
			}

			// add 'width' & 'height' attributes if missing
			if ( !isset( $attributes[ 'width' ] ) && !isset( $attributes[ 'height' ] ) && !empty( $attributes[ 'src' ] ) ) {
				$attachment_id = attachment_url_to_postid( $attributes[ 'src' ] );

				if ( is_int( $attachment_id ) ) {
					$attachment_meta = wp_get_attachment_metadata( $attachment_id );

					if ( isset( $attachment_meta[ 'width' ] ) ) {
						$attributes[ 'width' ] = $attachment_meta[ 'width' ];
					}
					if ( isset( $attachment_meta[ 'height' ] ) ) {
						$attributes[ 'height' ] = $attachment_meta[ 'height' ];
					}
				}
			}

			// change 'src' to 'data-src'
			$attributes[ 'data-src' ] = $attributes[ 'src' ];

			// change 'srcset' to 'data-srcset'
			if ( !empty( $attributes[ 'srcset' ] ) ) {
				$attributes[ 'data-srcset' ] = $attributes[ 'srcset' ];
				unset( $attributes[ 'srcset' ] );
			}

			// change 'sizes' to 'data-sizes'
			if ( !empty( $attributes[ 'sizes' ] ) ) {
				$attributes[ 'data-sizes' ] = $attributes[ 'sizes' ];
				unset( $attributes[ 'sizes' ] );
			}

			// set 'src' to placeholder
			if ( !empty( $attributes[ 'width' ] ) && !empty( $attributes[ 'height' ] ) ) {
				$attributes[ 'src' ] = forqy_image_placeholder( $attributes[ 'width' ], $attributes[ 'height' ] );
			} else {
				// check if 'src' is a file to prevent error 'Failed to open stream: HTTP request failed! HTTP/1.1 403 Forbidden' with getimagesize() function
				if ( is_file( $attributes[ 'src' ] ) ) {
					list ( $width, $height ) = getimagesize( $attributes[ 'src' ] );

					$attributes[ 'src' ]    = forqy_image_placeholder( $width, $height );
					$attributes[ 'width' ]  = $width;
					$attributes[ 'height' ] = $height;
				}
			}

			// add 'fetchpriority'
			if ( empty( $attributes[ 'fetchpriority' ] ) ) {
				$attributes[ 'fetchpriority' ] = 'low';
			}
		}

		// reformat attributes
		$attributes_s = array();

		foreach ( $attributes as $attribute => $value ) {

			if ( '' === $value ) {
				$attributes_s[] = sprintf( '%s', $attribute );
			} else {
				$attributes_s[] = sprintf( '%s="%s"', $attribute, esc_attr( $value ) );
			}

		}

		$attributes_s = implode( ' ', $attributes_s );

		// loading
		$loading = forqy_image_loading();

		return sprintf( '<img %1$s>%2$s', $attributes_s, $loading );

	}

}
