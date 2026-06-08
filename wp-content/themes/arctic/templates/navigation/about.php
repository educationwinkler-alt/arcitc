<?php

/**
 * About Navigation
 */

?>

<div class="f-links f-links--about f-links--sticky js-section-nav-handoff">
	<div class="f-links__container a-container">
		<nav class="f-links__navigation js-links__navigation"
		     aria-label="<?php echo esc_attr_x( 'About Navigation', 'navigation', 'baspa' ); ?>">
			<ul>
				<li>
					<a class="active" href="#<?php echo sanitize_title( esc_attr_x( 'our-company', 'anchor', 'baspa' ) ); ?>">
						<?php echo esc_html__( 'Naše společnost', 'baspa' ); ?>
					</a>
				</li>
				<li>
					<a href="#career">
						<?php echo esc_html__( 'Kariéra', 'baspa' ); ?>
					</a>
				</li>
			</ul>
		</nav>
	</div>
</div>
