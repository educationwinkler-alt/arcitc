<?php

/**
 * Progress
 */

$steps = array(
	array( 'Vaše poptávka', 'Ozvěte se nám přes kontaktní formulář, e-mail nebo telefonicky.' ),
	array( 'Konzultace', 'Doporučíme výběr bazénu nebo vířivky a návštěvu showroomu.' ),
	array( 'Nabídka', 'Na základě požadavků připravíme nezávaznou kalkulaci.' ),
	array( 'Uzavření smlouvy', 'Potvrdíme průběh dodávky, montáže a přípravy.' ),
	array( 'Stavební příprava', 'Poradíme vám, co a jak připravit před montáží.' ),
	array( 'Montáž', 'Rádi se postaráme o odbornou montáž a organizaci celé akce.' ),
);
?>

<section id="<?php echo sanitize_title( esc_attr_x( 'order-progress', 'anchor', 'baspa' ) ); ?>" class="f-section f-section--progress">
	<div class="f-section__container a-container">
		<div class="f-progress-layout">
			<div class="f-progress-layout__intro">
				<h2><?php echo esc_html__( 'Průběh zakázky', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Od první poptávky až po odbornou montáž vás provedeme celým procesem tak, aby byl výběr vířivky nebo bazénu jednoduchý a přehledný.', 'baspa' ); ?></p>
			</div>
			<ol class="f-progress-steps">
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
