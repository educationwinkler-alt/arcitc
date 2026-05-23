<?php

/**
 * Navigation Trigger
 */

?>

<button class="f-search__trigger f-off__trigger f-off__trigger--icon f-off__trigger--header f-button a-button a-button--icon a-off__trigger js-off__trigger a-show:s a-show:xs a-show:min"
        data-off="search"
        data-off-breakpoint="all"
        aria-expanded="false"
        aria-controls="<?php echo sanitize_title( esc_attr_x( 'search-dialog', 'anchor', 'baspa' ) ); ?>">
	<?php if ( function_exists( 'forqy_get_icon' ) ) {
		forqy_get_icon( 'search' );
	} ?>
	<span class="screen-reader-text"><?php echo esc_html__( 'Search', 'baspa' ); ?></span>
</button>
