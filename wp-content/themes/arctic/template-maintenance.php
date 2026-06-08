<?php
/**
 * Template Name: Figma Provoz a udrzba
 */

$post_id         = get_queried_object_id();
$article_content = trim( apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ) );

if ( '' === $article_content ) {
	$article_content = '
		<section>
			<h2>' . esc_html__( 'Náklady na vlastnictví a provozování vířivky', 'baspa' ) . '</h2>
			<p>' . esc_html__( 'Obsah této stránky je možné upravit přímo ve WordPress editoru stránky.', 'baspa' ) . '</p>
		</section>
		<section>
			<h2>' . esc_html__( 'Další inovace', 'baspa' ) . '</h2>
			<p>' . esc_html__( 'Doplňte text v administraci.', 'baspa' ) . '</p>
		</section>
		<section>
			<h2>' . esc_html__( 'Nejnižší provozní náklady', 'baspa' ) . '</h2>
			<p>' . esc_html__( 'Doplňte text v administraci.', 'baspa' ) . '</p>
		</section>
		<section>
			<h2>' . esc_html__( 'Skutečná ochrana proti mrazu', 'baspa' ) . '</h2>
			<p>' . esc_html__( 'Doplňte text v administraci.', 'baspa' ) . '</p>
		</section>';
}

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--maintenance">
	<section class="f-section f-section--figma-article">
		<div class="f-section__container a-container">
			<h2 class="screen-reader-text"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
			<div class="f-figma-article" data-content-source="wp-editor">
				<?php echo $article_content; ?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
