<?php

/**
 * Navigation Trigger
 */

?>

<button class="f-navigation__trigger a-button a-button--accent a-button--icon a-off__trigger js-off__trigger js-off__close a-show:min a-show:xs a-show:s a-show:m"
        data-off="navigation"
        data-off-breakpoint="1400"
        aria-expanded="false"
        aria-controls="<?php echo sanitize_title( esc_attr_x( 'navigation', 'anchor', 'baspa' ) ); ?>">

	<span class="f-icon__burger" aria-hidden="true">
		<span class="icon"><span></span><span></span><span></span></span>
	</span>
	<span class="screen-reader-text"><?php echo esc_html__( 'Navigation', 'baspa' ); ?></span>
</button>
