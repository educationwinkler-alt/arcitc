<?php

/**
 * Service Element
 */

?>

<div class="f-service">
	<div class="a-flex a-flex--align-center a-flex--justify-center a-gap--s">
		<div class="a-flex__item--100 a-flex__item:s">

			<div class="f-service__images">
				<figure class="f-service__image f-service__image--background f-image a-image a-image--cover">
					<img src="<?php echo forqy_image_placeholder( 960, 540 ); ?>"
					     data-src="<?php echo get_theme_file_uri( 'images/servis.jpg' ); ?>"
					     alt="<?php echo esc_html__( 'Service', 'baspa' ); ?>"
					     width="960" height="540"
					     class="f-lazy js-lazy">
				</figure>
				<figure class="f-service__image f-service__image--overlap">
					<img src="<?php echo forqy_image_placeholder( 480, 320 ); ?>"
					     data-src="<?php echo get_theme_file_uri( 'images/servisak.png' ); ?>"
					     alt="<?php echo esc_html__( 'Serviceman', 'baspa' ); ?>"
					     width="480" height="320"
					     class="f-lazy js-lazy">
				</figure>
			</div>

		</div>
		<div class="a-flex__item--100 a-flex__item--auto:m">

			<div class="f-service__container">
				<div class="a-flex a-flex--align-center a-flex--justify-center">
					<div class="a-flex__item">

						<div class="a-stack a-stack--row a-stack--align-center a-gap--xs">
							<?php get_template_part( 'images/icon/plus', 'green' ); ?>
							<h3><?php echo esc_html__( 'Service', 'baspa' ); ?></h3>
						</div>

					</div>
					<div class="a-flex__item--100 a-flex__item--auto:m">
						<p><?php echo esc_html__( 'Jsme tu pro vás i po dokončení projektu. Záruční i pozáruční servis je pro nás samozřejmost.', 'baspa' ); ?></p>
					</div>
					<?php if ( !empty( get_page_by_path( 'sluzby/servis' ) ) ) { ?>
						<div class="a-flex__item--100 a-flex__item:m">
							<a href="<?php echo get_permalink( get_page_by_path( 'sluzby/servis' ) ); ?>">
								<strong><?php echo esc_html__( 'More information', 'baspa' ); ?></strong>
							</a>
						</div>
					<?php } ?>
				</div>
			</div>

		</div>
	</div>
</div>
