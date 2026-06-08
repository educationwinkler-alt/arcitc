<?php
/**
 * Template Name: Figma Záruka
 */

$post_id = get_queried_object_id();

$default_warranty_tiers = array(
	array(
		'name'  => __( 'Custom', 'baspa' ),
		'items' => array(
			__( 'Skořepina', 'baspa' )      => __( 'Doživotní', 'baspa' ),
			__( 'Akrylát', 'baspa' )        => __( '5 let', 'baspa' ),
			__( 'Podlaha', 'baspa' )        => __( 'Doživotní', 'baspa' ),
			__( 'Náhradní díly', 'baspa' )  => __( '5 let', 'baspa' ),
			__( 'Práce', 'baspa' )          => __( '5 let', 'baspa' ),
		),
	),
	array(
		'name'  => __( 'Classic', 'baspa' ),
		'items' => array(
			__( 'Skořepina', 'baspa' )      => __( '10 let', 'baspa' ),
			__( 'Akrylát', 'baspa' )        => __( '4 roky', 'baspa' ),
			__( 'Podlaha', 'baspa' )        => __( '3 roky', 'baspa' ),
			__( 'Náhradní díly', 'baspa' )  => __( '3 roky', 'baspa' ),
			__( 'Práce', 'baspa' )          => __( '3 roky', 'baspa' ),
		),
	),
	array(
		'name'  => __( 'Core', 'baspa' ),
		'items' => array(
			__( 'Skořepina', 'baspa' )      => __( '7 let', 'baspa' ),
			__( 'Akrylát', 'baspa' )        => __( '1 rok', 'baspa' ),
			__( 'Podlaha', 'baspa' )        => __( '3 roky', 'baspa' ),
			__( 'Náhradní díly', 'baspa' )  => __( '3 roky', 'baspa' ),
			__( 'Práce', 'baspa' )          => __( '1 rok', 'baspa' ),
		),
	),
);

$labels = array(
	'shell'   => __( 'Skořepina', 'baspa' ),
	'acrylic' => __( 'Akrylát', 'baspa' ),
	'floor'   => __( 'Podlaha', 'baspa' ),
	'parts'   => __( 'Náhradní díly', 'baspa' ),
	'labor'   => __( 'Práce', 'baspa' ),
);

$tier_rows = array();

foreach ( get_post_meta( $post_id, 'warranty_tiers' ) as $raw_tier_row ) {
	if ( !is_array( $raw_tier_row ) ) {
		continue;
	}

	if ( array_key_exists( 'name', $raw_tier_row ) ) {
		$tier_rows[] = $raw_tier_row;
		continue;
	}

	foreach ( $raw_tier_row as $nested_tier_row ) {
		if ( is_array( $nested_tier_row ) ) {
			$tier_rows[] = $nested_tier_row;
		}
	}
}

$warranty_tiers = array();

foreach ( $tier_rows as $tier_row ) {
	$name = trim( wp_strip_all_tags( (string) ( $tier_row['name'] ?? '' ) ) );

	if ( '' === $name ) {
		continue;
	}

	$items = array();
	foreach ( $labels as $key => $label ) {
		$value = trim( wp_strip_all_tags( (string) ( $tier_row[ $key ] ?? '' ) ) );

		if ( '' !== $value ) {
			$items[ $label ] = $value;
		}
	}

	if ( !empty( $items ) ) {
		$warranty_tiers[] = array(
			'name'  => $name,
			'items' => $items,
		);
	}
}

if ( empty( $warranty_tiers ) ) {
	$warranty_tiers = $default_warranty_tiers;
}

$warranty_labels = array_keys( $warranty_tiers[0]['items'] );
$default_note    = sprintf(
	'%s<br><br>%s',
	esc_html__( 'Dopravné: první dva roky záruky hradí cestu servisního technika prodávající a od třetího roku tuto platí zákazník, ať jde o opravu dílů spadajících do prodloužené záruky, nebo o pozáruční servis.', 'baspa' ),
	sprintf(
		__( 'Konkrétní záruční podmínky naleznete v uživatelském manuálu, viz sekce %s.', 'baspa' ),
		'<a href="' . esc_url( home_url( '/ke-stazeni/' ) ) . '">' . esc_html__( 'Ke stažení', 'baspa' ) . '</a>'
	)
);
$warranty_note   = trim( (string) get_post_meta( $post_id, 'warranty_note', true ) );
$warranty_note   = '' !== $warranty_note ? apply_filters( 'the_content', $warranty_note ) : $default_note;

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--warranty">
	<section class="f-section f-section--warranty-cards">
		<div class="f-section__container a-container">
			<h2 class="screen-reader-text"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
			<div class="f-warranty-cards" data-content-source="warranty-meta">
				<div class="f-warranty-labels" aria-hidden="true">
					<?php foreach ( $warranty_labels as $label ) { ?>
						<span><?php echo esc_html( $label ); ?></span>
					<?php } ?>
				</div>
				<?php foreach ( $warranty_tiers as $tier ) { ?>
					<article class="f-warranty-card" data-asset-status="WAITING_ON_OWNER">
						<div class="f-warranty-card__media f-warranty-card__media--waiting" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Produktová fotografie pro řadu %s čeká na owner podklady.', 'baspa' ), $tier['name'] ) ); ?>">
							<span><?php echo esc_html__( 'Čeká na foto', 'baspa' ); ?></span>
						</div>
						<h2 class="f-warranty-card__name"><?php echo esc_html( $tier['name'] ); ?></h2>
						<dl class="f-warranty-card__items">
							<?php foreach ( $tier['items'] as $label => $value ) { ?>
								<div class="f-warranty-card__item">
									<dt><?php echo esc_html( $label ); ?></dt>
									<dd><?php echo esc_html( $value ); ?></dd>
								</div>
							<?php } ?>
						</dl>
					</article>
				<?php } ?>
				<div class="f-warranty-note">
					<?php echo wp_kses_post( $warranty_note ); ?>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
