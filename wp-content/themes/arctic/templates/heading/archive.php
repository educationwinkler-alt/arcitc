<?php

/**
 * Archive Heading
 */

$heading_class = array( 'f-heading', 'f-heading--archive' );
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--s">

			<h1><?php if ( is_category() ) {
					single_cat_title();
				} else if ( is_tag() ) {
					single_tag_title();
				} else if ( is_tax() ) {
					single_term_title();
				} else if ( is_author() ) {
					if ( get_the_author_meta( 'user_url' ) ) {
						echo '<a href="' . get_the_author_meta( 'user_url' ) . '" target="_blank" rel="noopener">' . get_the_author() . '<span class="screen-reader-text">' . _x( '(opens in new tab)', 'link to a new tab', 'baspa' ) . '</span></a>';
					} else {
						echo get_the_author();
					}
				} else if ( is_day() ) {
					the_time( esc_html__( 'F jS, Y', 'baspa' ) );
				} else if ( is_month() ) {
					the_time( esc_html__( 'F, Y', 'baspa' ) );
				} else if ( is_year() ) {
					the_time( esc_html__( 'Y', 'baspa' ) );
				} else {
					esc_html_e( 'Archive', 'baspa' );
				} ?></h1>

			<?php if ( is_category() || is_tax() ) { ?>
				<div class="f-heading__description">
					<?php echo term_description(); ?>
				</div>
			<?php } ?>

		</div>

	</div>
</header>
