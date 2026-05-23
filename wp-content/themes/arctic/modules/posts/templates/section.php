<?php

/**
 * Section
 */

// Query Arguments
$posts_query_args = array(
	'post_type'   => 'post',
	'post_status' => 'publish',
);

// Query
$posts_query = new WP_Query( $posts_query_args );

if ( $posts_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'posts', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--posts">

		<div class="f-section__container a-container">

			<header class="f-section__header">

				<div class="a-flex a-flex--align-center">
					<div class="a-flex__item--auto">

						<h2><?php echo esc_html__( 'Posts', 'baspa' ); ?></h2>

					</div>
					<?php if ( function_exists( 'forqy_get_page_by_template' ) && !empty( forqy_get_page_by_template( 'template-blog.php' ) ) ) { ?>
						<div class="a-flex__item">

							<div class="f-section__actions a-buttons">
								<a class="f-section__button f-button f-button--secret a-button a-button--link"
								   href="<?php echo forqy_get_page_by_template( 'template-blog.php' )[ 'permalink' ]; ?>">
									<?php
									echo wp_kses_post( __( 'All Posts', 'baspa' ) );
									forqy_get_icon( 'caret-right--small' );
									?>
								</a>
							</div>

						</div>
					<?php } ?>
				</div>

			</header>

			<?php
			get_template_part( 'templates/loop', '', array(
				'query_args'           => $posts_query_args,
				'query_pagination'     => false,
				'query_posts_per_page' => 3,
			) );
			?>

		</div>

	</section>

<?php }
