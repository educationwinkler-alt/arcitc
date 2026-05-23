<?php

/**
 * Heading Badge
 */

$badge_title = get_post_meta( get_the_ID(), 'page_badge_title', true );
$badge_text  = get_post_meta( get_the_ID(), 'page_badge_text', true );

if ( !empty( $badge_title ) ) { ?>
	<div class="f-heading__badge">
		<strong><?php echo wp_kses_post( $badge_title ); ?></strong>
		<?php if ( !empty( $badge_text ) ) { ?>
			<span><?php echo wp_kses_post( $badge_text ); ?></span>
		<?php } ?>
	</div>
<?php }
