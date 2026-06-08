<?php

/**
 * Term Heading
 */

$term          = get_queried_object();
$term_id       = get_queried_object_id();
$taxonomy      = $term instanceof WP_Term ? $term->taxonomy : '';
$hero_prefix   = function_exists( 'arctic_hero_term_prefix' ) ? arctic_hero_term_prefix( $taxonomy ) : 'category_heading';
$heading_title = get_term_meta( $term_id, $hero_prefix . '_title', true );
$heading_title = !empty( $heading_title ) ? $heading_title : single_term_title( '', false );
$heading_text  = get_term_meta( $term_id, $hero_prefix . '_text', true );
$heading_cta   = get_term_meta( $term_id, $hero_prefix . '_cta_text', true );
$heading_cta   = !empty( $heading_cta ) ? $heading_cta : __( 'Vybrat vířivku', 'baspa' );

$has_media     = function_exists( 'arctic_term_has_hero_media' )
	? arctic_term_has_hero_media( $term_id, $taxonomy )
	: (bool) get_term_meta( $term_id, $hero_prefix . '_image', true );

$heading_class   = array( 'f-heading', 'f-heading--term' );
$heading_class[] = $has_media ? 'f-heading--background' : '';
?>

<header <?php ( !function_exists( 'forqy_class' ) ) ?: forqy_class( $heading_class ); ?>>
	<div class="f-heading__container a-container">
		<?php if ( is_tax( 'product-category' ) ) {
			$term_link = get_term_link( $term_id );
			$term_link = !is_wp_error( $term_link ) ? $term_link : home_url( '/' );
			?>
			<nav class="f-breadcrumbs f-breadcrumbs--figma" aria-label="<?php echo esc_attr_x( 'Breadcrumbs', 'breadcrumbs', 'baspa' ); ?>">
				<ol>
					<li>
						<a class="f-breadcrumbs__home" href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<span class="a-sr-only"><?php echo esc_html__( 'Úvod', 'baspa' ); ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( $term_link ); ?>" aria-current="page">
							<?php echo esc_html( $heading_title ); ?>
						</a>
					</li>
				</ol>
			</nav>
		<?php } else if ( function_exists( 'forqy_breadcrumbs' ) ) {
			forqy_breadcrumbs();
		} ?>

		<div class="f-heading__headline a-stack a-stack--align-start a-gap--s">
			<h1><?php echo esc_html( $heading_title ); ?></h1>

			<?php if ( !empty( $heading_text ) || term_description() ) { ?>
				<div class="f-heading__description">
					<?php echo !empty( $heading_text ) ? wp_kses_post( wpautop( $heading_text ) ) : term_description(); ?>
				</div>
			<?php } ?>

			<?php get_template_part( 'templates/button/contact', '', array(
				'text'          => $heading_cta,
				'class_replace' => array(
					'f-button',
					'a-button',
					'a-button--accent',
					'f-off__trigger',
					'js-off__trigger',
				),
			) ); ?>
		</div>
	</div>

	<?php get_template_part( 'templates/image/background' ); ?>
</header>
