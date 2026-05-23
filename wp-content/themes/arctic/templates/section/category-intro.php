<?php

/**
 * Category Intro
 */

$showroom_image = get_posts( array(
	'post_type'      => 'attachment',
	'post_status'    => 'inherit',
	'posts_per_page' => 1,
	'fields'         => 'ids',
	'meta_key'       => '_arctic_seed_key',
	'meta_value'     => 'figma-node-1-273-category-vlastnosti',
) );

$category_image = get_posts( array(
	'post_type'      => 'attachment',
	'post_status'    => 'inherit',
	'posts_per_page' => 1,
	'fields'         => 'ids',
	'meta_key'       => '_arctic_seed_key',
	'meta_value'     => 'figma-node-1-274-category-zaruka',
) );
?>

<section class="f-section f-section--category-intro">
	<div class="f-section__container a-container">
		<div class="f-category-intro f-category-intro--split">
			<div class="f-category-intro__content">
				<h2><?php echo esc_html__( 'Vlastnosti vířivek', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Venkovní vířivky Arctic Spas jsou navržené a vyrobené pro drsné podnebí severní Kanady. Izolace, technologie a úprava vody pomáhají držet nízké provozní náklady a pohodlnou údržbu.', 'baspa' ); ?></p>
				<a class="f-button a-button a-button--accent" href="<?php echo esc_url( home_url( '/vlastnosti/' ) ); ?>">
					<?php echo esc_html__( 'Více o vlastnostech', 'baspa' ); ?>
				</a>
			</div>
			<?php if ( !empty( $showroom_image ) ) { ?>
				<figure class="f-category-intro__image">
					<?php echo wp_get_attachment_image( (int) $showroom_image[0], 'large' ); ?>
				</figure>
			<?php } ?>
		</div>

		<div class="f-category-intro f-category-intro--reverse">
			<?php if ( !empty( $category_image ) ) { ?>
				<figure class="f-category-intro__image">
					<?php echo wp_get_attachment_image( (int) $category_image[0], 'large' ); ?>
				</figure>
			<?php } ?>
			<div class="f-category-intro__content">
				<h2><?php echo esc_html__( 'Záruka', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Na rozdíl od běžných vířivek nejsou pro nás výjimkou instalace v horském nebo celoročním provozu. Arctic Spas stojí na pevném skeletu, izolaci a servisovatelných technologiích.', 'baspa' ); ?></p>
				<a class="f-button a-button a-button--accent" href="<?php echo esc_url( home_url( '/zaruka/' ) ); ?>">
					<?php echo esc_html__( 'Více o záruce', 'baspa' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
