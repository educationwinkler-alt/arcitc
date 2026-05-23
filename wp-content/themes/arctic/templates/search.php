<?php

/**
 * Search
 *
 * @requires pavelrich/off
 */

?>

<aside id="<?php echo sanitize_title( esc_attr_x( 'search-dialog', 'anchor', 'baspa' ) ); ?>"
       class="f-off--search f-off f-off--dialog f-off--center a-off a-off--dialog js-off"
       data-off="search"
       data-off-breakpoint="all"
       data-off-relocate="false"
       aria-labelledby="search-heading"
       aria-hidden="true">

	<div class="f-off__container a-off__container a-off__container--75">

		<button class="f-off__close a-off__close js-off__close"
		        aria-controls="<?php echo sanitize_title( esc_attr_x( 'search-dialog', 'anchor', 'baspa' ) ); ?>"
		        data-off="search">
			<?php if ( function_exists( 'forqy_get_icon' ) ) {
				forqy_get_icon( 'close' );
			} ?>
			<span class="screen-reader-text"><?php echo esc_html__( 'Close', 'baspa' ); ?></span>
		</button>

		<header class="f-off__header a-off__header screen-reader-text">
			<h2 id="search-heading">
				<?php echo esc_html_x( 'Search', 'label', 'baspa' ); ?>
			</h2>
		</header>

		<div class="f-off__search">
			<?php
			set_query_var( 'baspa_search_id', 'search-off' );
			get_search_form();
			?>
		</div>

	</div>

</aside>
