<?php

/**
 * Heading
 */

$references_page = forqy_get_page_by_template( 'template-references.php' );

$title    = get_post_meta( get_the_ID(), 'page_title_text', true );
$subtitle = get_post_meta( get_the_ID(), 'page_subtitle_text', true );

$heading_class = array( 'f-heading' );
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			if ( is_tax() && !empty( $references_page ) ) {
				$text = $references_page[ 'title' ];
				$url  = $references_page[ 'permalink' ];
				forqy_breadcrumbs( $text, $url );
			} else {
				forqy_breadcrumbs();
			}
		} ?>

		<div class="a-stack">

			<h1>
				<?php
				if ( is_tax() ) {
					if ( !empty( $references_page ) ) {
						echo $references_page[ 'title' ];
					} else {
						single_term_title();
					}
				} else {
					if ( !empty( $title ) ) {
						echo wp_kses_post( $title );
					} else {
						the_title();
					}
				} ?>
			</h1>

			<?php if ( !empty( $subtitle ) ) { ?>
				<small class="f-heading__subtitle">
					<?php echo wp_kses_post( $subtitle ); ?>
				</small>
			<?php } ?>

		</div>

	</div>
</header>
