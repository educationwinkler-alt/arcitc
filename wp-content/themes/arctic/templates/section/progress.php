<?php

/**
 * Progress
 */

$post_id = get_queried_object_id();

$default_steps = array(
	array( 'Vaše poptávka', 'Ozvěte se nám přes kontaktní formulář, e-mail nebo telefonicky.' ),
	array( 'Konzultace', 'Doporučíme výběr bazénu nebo vířivky a návštěvu showroomu.' ),
	array( 'Nabídka', 'Na základě požadavků připravíme nezávaznou kalkulaci.' ),
	array( 'Uzavření smlouvy', 'Potvrdíme průběh dodávky, montáže a přípravy.' ),
	array( 'Stavební příprava', 'Poradíme vám, co a jak připravit před montáží.' ),
	array( 'Montáž', 'Rádi se postaráme o odbornou montáž a organizaci celé akce.' ),
);

$title = function_exists( 'arctic_meta_text' ) ? arctic_meta_text( $post_id, 'homepage_progress_title' ) : '';
$text  = function_exists( 'arctic_meta_text' ) ? arctic_meta_text( $post_id, 'homepage_progress_text' ) : '';
$steps = array();

$raw_steps = function_exists( 'arctic_meta_fieldset_rows' ) ? arctic_meta_fieldset_rows( $post_id, 'homepage_progress_steps', array( 'title', 'text' ) ) : array();
foreach ( $raw_steps as $raw_step ) {
	if ( !is_array( $raw_step ) ) {
		continue;
	}

	$step_title = trim( wp_strip_all_tags( (string) ( $raw_step['title'] ?? '' ) ) );
	$step_text  = trim( wp_strip_all_tags( (string) ( $raw_step['text'] ?? '' ) ) );

	if ( '' === $step_title && '' === $step_text ) {
		continue;
	}

	$steps[] = array( $step_title, $step_text );
}

$source = 'homepage-meta';

if ( '' === $title && '' === $text && empty( $steps ) ) {
	if ( !function_exists( 'arctic_allow_seed_fallbacks' ) || !arctic_allow_seed_fallbacks() ) {
		return;
	}

	$source = 'figma-fallback';
	$title  = __( 'Průběh zakázky', 'baspa' );
	$text   = __( 'Od první poptávky až po odbornou montáž vás provedeme celým procesem tak, aby byl výběr vířivky nebo bazénu jednoduchý a přehledný.', 'baspa' );
	$steps  = $default_steps;
}
?>

<section id="order-progress" class="f-section f-section--progress" data-content-source="<?php echo esc_attr( $source ); ?>">
	<div class="f-section__container a-container">
		<div class="f-progress-layout f-progress-layout--shared">
			<div class="f-progress-layout__intro" data-content-source="<?php echo esc_attr( $source ); ?>">
				<h2><?php echo esc_html( $title ); ?></h2>
				<p><?php echo esc_html( $text ); ?></p>
			</div>
			<ol class="f-progress-steps" data-content-source="<?php echo esc_attr( $source ); ?>">
				<?php foreach ( $steps as $index => $step ) { ?>
					<li>
						<strong><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></strong>
						<div>
							<h3><?php echo esc_html( $step[0] ); ?></h3>
							<p><?php echo esc_html( $step[1] ); ?></p>
						</div>
					</li>
				<?php } ?>
			</ol>
		</div>
	</div>
</section>
