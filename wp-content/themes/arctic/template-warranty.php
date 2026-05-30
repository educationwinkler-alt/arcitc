<?php
/**
 * Template Name: Figma Záruka
 */

$warranty_tiers = array(
	array(
		'name'  => __( 'Custom', 'baspa' ),
		'items' => array(
			__( 'Skořepina', 'baspa' )     => __( 'Doživotní', 'baspa' ),
			__( 'Akrylát', 'baspa' )       => __( '5 let', 'baspa' ),
			__( 'Podlaha', 'baspa' )       => __( 'Doživotní', 'baspa' ),
			__( 'Náhradní díly', 'baspa' ) => __( '5 let', 'baspa' ),
			__( 'Práce', 'baspa' )         => __( '5 let', 'baspa' ),
		),
	),
	array(
		'name'  => __( 'Classic', 'baspa' ),
		'items' => array(
			__( 'Skořepina', 'baspa' )     => __( '10 let', 'baspa' ),
			__( 'Akrylát', 'baspa' )       => __( '4 roky', 'baspa' ),
			__( 'Podlaha', 'baspa' )       => __( '3 roky', 'baspa' ),
			__( 'Náhradní díly', 'baspa' ) => __( '3 roky', 'baspa' ),
			__( 'Práce', 'baspa' )         => __( '3 roky', 'baspa' ),
		),
	),
	array(
		'name'  => __( 'Core', 'baspa' ),
		'items' => array(
			__( 'Skořepina', 'baspa' )     => __( '7 let', 'baspa' ),
			__( 'Akrylát', 'baspa' )       => __( '1 rok', 'baspa' ),
			__( 'Podlaha', 'baspa' )       => __( '3 roky', 'baspa' ),
			__( 'Náhradní díly', 'baspa' ) => __( '3 roky', 'baspa' ),
			__( 'Práce', 'baspa' )         => __( '1 rok', 'baspa' ),
		),
	),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--warranty">
	<section class="f-section f-section--warranty-cards">
		<div class="f-section__container a-container">
			<div class="f-warranty-cards">
				<?php foreach ( $warranty_tiers as $tier ) { ?>
					<div class="f-warranty-card">
						<h2 class="f-warranty-card__name"><?php echo esc_html( $tier['name'] ); ?></h2>
						<dl class="f-warranty-card__items">
							<?php foreach ( $tier['items'] as $label => $value ) { ?>
								<dt><?php echo esc_html( $label ); ?></dt>
								<dd><?php echo esc_html( $value ); ?></dd>
							<?php } ?>
						</dl>
					</div>
				<?php } ?>
			</div>
			<p class="f-warranty-note"><?php echo esc_html__( 'Dopravné: první dva roky záruky hradí cestu servisního technika prodávající a od třetího roku tuto platí zákazník, ať jde o opravu dílů spadajících do prodloužené záruky, nebo o pozáruční servis. Konkrétní záruční podmínky naleznete v uživatelském manuálu v sekci Ke stažení.', 'baspa' ); ?></p>
		</div>
	</section>
</main>

<?php
get_footer();
