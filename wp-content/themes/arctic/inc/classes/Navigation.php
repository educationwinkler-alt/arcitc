<?php

class Baspa_Walker_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * Start Element
	 *
	 * @param string &$output
	 * @param $data_object
	 * @param int $depth
	 * @param null $args
	 * @param int $current_object_id
	 *
	 * @return void
	 */
	function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ): void {

		// Depth-dependent classes
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' );
//		$depth_class = ( $depth == 0 ? '' : 'sub-menu-item' ); // if depth-zero, we shouldn't add a class
//		$depth_class .= ( $depth >= 2 ? ' sub-sub-menu-item' : '' ); // if depth is 2 or more, add a class

//		do_action('qm/debug', $data_object);

//		if ( isset( $data_object->object ) && $data_object->object == 'product-category' ) {
//			do_action( 'qm/debug', $data_object );
//		}

		// Passed classes
		$classes = empty( $data_object->classes ) ? array() : (array)$data_object->classes;
		if ( isset( $data_object->object ) && $data_object->object == 'product-category' ) {
			$classes[] = 'has-sub';
		}

		// Join class names with a space
		$class_names = ' class="' . esc_attr( join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $data_object, $args ) ) ) . '"';

		// Output the start of the list item
		$output .= $indent . '<li id="menu-item-' . $data_object->ID . '"' . $class_names . '>';

		// Link attributes
		$attributes = !empty( $data_object->url ) ? ' href="' . esc_attr( $data_object->url ) . '"' : '';

		// Output the link and caret
		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $data_object->title, $data_object->ID ) . $args->link_after;
		$item_output .= '</a>';
//		do_action( 'qm/debug', $data_object );
		if ( isset( $data_object->object ) && $data_object->object == 'product-category' ) {
			$item_output .= '<ul class="f-navigation-sub">';
			ob_start();
			if ( isset( $data_object->object_id ) ) {
				$children = get_terms( array(
					'taxonomy'   => 'product-category',
					'parent'     => $data_object->object_id,
					'hide_empty' => false,
				) );
				if ( !empty( $children ) && !is_wp_error( $children ) ) { ?>
					<div class="f-navigation-sub__container a-container">
						<?php get_template_part( 'modules/products/templates/navigation/list', '', array(
							'term_parent_id' => $data_object->object_id,
						) ); ?>
					</div>
				<?php } else { ?>
					<div class="f-navigation-sub__container a-container">
						<?php get_template_part( 'modules/products/templates/navigation/products', '', array(
							'term_parent_id' => $data_object->object_id,
						) ); ?>
					</div>
				<?php }
			}
			$item_output .= '<li>' . ob_get_clean() . '</li>';
			$item_output .= '</ul>';
		}
		$item_output .= $args->after;

		// Output the end of the list item.
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $data_object, $depth, $args );
	}
}
