<?php

/**
 * About Navigation
 */

?>

<div class="f-links f-links--sticky f-links--about">
	<div class="f-links__container a-container">
		<nav class="f-links__navigation js-links__navigation"
		     aria-label="<?php echo esc_attr_x( 'About Navigation', 'navigation', 'baspa' ); ?>">
			<ul>
				<?php if ( get_the_content() ) { ?>
					<li>
						<a href="#<?php echo sanitize_title( esc_attr_x( 'our-company', 'anchor', 'baspa' ) ); ?>">
							<?php echo esc_html__( 'Our Company', 'baspa' ); ?>
						</a>
					</li>
				<?php } ?>
				<?php if ( baspa_partners_exists() ) { ?>
					<li>
						<a href="#<?php echo sanitize_title( esc_attr_x( 'suppliers-and-partners', 'anchor', 'baspa' ) ); ?>">
							<?php echo esc_html__( 'Suppliers and Partners', 'baspa' ); ?>
						</a>
					</li>
				<?php } ?>
				<?php //if ( baspa_jobs_exists() ) { ?>
					<li>
						<a href="#<?php echo sanitize_title( esc_attr_x( 'career', 'anchor', 'baspa' ) ); ?>">
							<?php echo esc_html__( 'Career', 'baspa' ); ?>
						</a>
					</li>
				<?php //} ?>
			</ul>
		</nav>
	</div>
</div>