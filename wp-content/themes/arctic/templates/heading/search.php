<?php

/**
 * Search Heading
 */

global $wp_query;
?>

<header class="f-heading f-heading--search">
	<div class="f-heading__container a-container">
			<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
				forqy_breadcrumbs();
			} ?>

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--s">

			<h1><?php
				printf(
					esc_html(
					/* translators: %1$s: the number of search results, %2$s: search query */
						_n(
							'%1$s result for \'%2$s\'',
							'%1$s results for \'%2$s\'',
							$wp_query->found_posts,
							'baspa'
						)
					),
					$wp_query->found_posts,
					esc_html( get_search_query() )
				); ?></h1>
		</div>

	</div>
</header>
