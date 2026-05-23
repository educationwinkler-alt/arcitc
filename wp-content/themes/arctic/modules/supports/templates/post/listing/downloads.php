<?php

/**
 * Post Listing Downloads
 */

$downloads = get_post_meta( get_the_ID(), 'support_downloads' );

if ( !empty( $downloads ) ) { ?>

	<div class="f-support__downloads a-stack a-gap--xxs">
		<?php foreach ( $downloads as $download_id ) {
			$download = get_post( $download_id );

			do_action( 'qm/debug', $download );
			?>
			<article class="f-listing f-listing--download">
				<div class="f-listing__container">
					<div class="a-flex a-flex--align-center a-gap--s">
						<div class="a-flex__item--100 a-flex__item--auto:m">

							<div class="a-stack a-gap--xxxs">
								<header class="f-listing__header">
									<h4>
										<?php if ( !empty( $download->post_excerpt ) ) {
											echo esc_html( $download->post_excerpt );
										} else {
											echo esc_html( $download->post_title );
										} ?>
									</h4>
								</header>
								<?php if ( !empty( $download->post_content ) ) { ?>
									<div class="f-listing__excerpt">
										<?php echo wp_kses_post( $download->post_content ); ?>
									</div>
								<?php } ?>
							</div>

						</div>
						<?php if ( !empty( $download->guid ) ) { ?>
							<div class="a-flex__item--100 a-flex__item:m">

								<a href="<?php echo esc_url( $download->guid ); ?>"
								   class="f-button a-button a-button--outline a-button--xs"
								   target="_blank"
								   rel="nofollow noopener">
									<?php
									get_template_part( 'images/icon/download' );
									echo esc_html__( 'Download', 'baspa' );
									?>
								</a>

							</div>
						<?php } ?>
					</div>
				</div>
			</article>
		<?php } ?>
	</div>

<?php }
