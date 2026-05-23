<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-faq', 'wp-block-details', 'is-style-icon' );
?>

<details id="faq-<?php the_ID(); ?>" name="faq" <?php post_class( $post_class ); ?>>

	<summary class="f-faq__summary f-summary">
		<span class="f-faq__icon f-summary__icon" aria-hidden="true"></span>
		<h2><?php the_title(); ?></h2>
	</summary>

	<div class="f-faq__content f-content">
		<?php the_content(); ?>
	</div>
</details>
