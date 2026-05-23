<?php

/**
 * Listing Header
 */

$title_meta_key = $args[ 'title_meta_key' ] ?? 'offer_title';

$title_alter = get_post_meta( get_the_ID(), $title_meta_key, true );
if ( !empty( $title_alter ) ) {
	$title = $title_alter;
} else {
	$title = get_the_title();
}

if ( is_home() || is_front_page() ) { ?>
	<h3><a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( $title ); ?></a></h3>
<?php } else { ?>
	<h2><a href="<?php the_permalink(); ?>"><?php echo wp_kses_post( $title ); ?></a></h2>
<?php }
