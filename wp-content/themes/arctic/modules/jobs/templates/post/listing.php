<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-job', 'wp-block-details', 'is-style-icon' );
?>

<details id="job-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<summary class="f-job__summary f-summary">
		<span class="f-job__icon f-summary__icon" aria-hidden="true"></span>
		<h2><?php the_title(); ?></h2>
	</summary>

	<div class="f-job__content f-content">
		<?php the_content(); ?>
	</div>
</details>
