<?php

/**
 * Post Single Heading
 */

?>

<header class="f-heading f-heading--single f-single__heading">
	<div class="f-heading__container a-container">
		<?php if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="a-stack a-container--75">

			<div class="a-stack a-gap--xs">
				<?php get_template_part( 'modules/posts/templates/post/common/categories' ); ?>

				<h1><?php the_title(); ?></h1>
			</div>

		</div>

	</div>
</header>
