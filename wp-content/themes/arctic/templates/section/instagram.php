<?php

/**
 * Instagram Section
 */

$instagram_shortcode = get_theme_mod( 'baspa_instagram_shortcode' );
$instagram_url       = get_theme_mod( 'baspa_instagram' );
$facebook_url        = get_theme_mod( 'baspa_facebook' );
$youtube_url         = get_theme_mod( 'baspa_youtube' );

if ( ( shortcode_exists( 'instagram-feed' ) || shortcode_exists( 'instagram' ) || shortcode_exists( 'insta-gallery' ) ) && !empty( $instagram_shortcode ) ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'instagram', 'anchor', 'baspa' ) ); ?>" class="f-section f-section--instagram f-section--soft">
		<div class="f-section__container a-container">

			<div class="a-flex a-flex--align-center a-flex--justify-between">
				<header class="f-section__header a-flex__item--100 a-flex__item:m">
					<h2 class="a-stack a-stack--row a-stack--justify-center a-gap--xs">
						<?php if ( !empty( $instagram_url ) ) { ?>
							<a href="<?php echo esc_url( $instagram_url ); ?>"
							   class="a-stack a-stack--row a-stack--align-center a-gap--xs"
							   target="_blank" rel="noreferrer nofollow external">
								<?php
								echo get_template_part( 'images/icon/social/instagram' );
								echo esc_html__( 'Arctic Spas on Instagram', 'baspa' );
								?>
							</a>
						<?php } else {
							echo get_template_part( 'images/icon/social/instagram' );
							echo esc_html__( 'Arctic Spas on Instagram', 'baspa' );
						} ?>
					</h2>
				</header>

				<?php if ( !empty( $facebook_url ) || !empty( $youtube_url ) ) { ?>
					<div class="a-flex__item--100 a-flex__item:m">
						<div class="f-section__links a-stack a-stack--row">
							<?php if ( !empty( $facebook_url ) ) { ?>
								<a href="<?php echo esc_url( $facebook_url ); ?>"
								   class="a-stack a-stack--row a-stack--align-center a-gap--xxs"
								   target="_blank" rel="noreferrer nofollow external">
									<?php get_template_part( 'images/icon/social/facebook' ) ?>
									<?php echo esc_html__( 'Facebook', 'baspa' ); ?>
								</a>
							<?php }
							if ( !empty( $youtube_url ) ) { ?>
								<a href="<?php echo esc_url( $youtube_url ); ?>"
								   class="a-stack a-stack--row a-stack--align-center a-gap--xxs"
								   target="_blank" rel="noreferrer nofollow external">
									<?php get_template_part( 'images/icon/social/youtube' ) ?>
									<?php echo esc_html__( 'YouTube', 'baspa' ); ?>
								</a>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>

			<div class="f-section__feed">
				<?php echo apply_shortcodes( esc_attr( $instagram_shortcode ) ); ?>
			</div>

		</div>
	</section>

<?php }
