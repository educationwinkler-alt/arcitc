<?php

/**
 * Listing
 */

// Class
$post_class = array( 'f-support', 'wp-block-details', 'is-style-icon', 'support-category-all' );
?>

<details id="support-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<summary class="f-support__summary f-summary">
		<span class="f-support__icon f-summary__icon" aria-hidden="true"></span>
		<h3><?php the_title(); ?></h3>
		<?php get_template_part( 'modules/supports/templates/post/listing/categories' ); ?>
	</summary>

	<div class="a-stack a-gap--s">
		<?php if ( get_the_content() ) { ?>
			<div class="f-support__content f-content">
				<?php the_content(); ?>
			</div>
		<?php }
		get_template_part( 'modules/supports/templates/post/listing/downloads' );
		?>
	</div>
</details>
