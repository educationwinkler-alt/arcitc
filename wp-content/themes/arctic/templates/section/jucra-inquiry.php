<?php
/**
 * Arctic Jucra/Visao pricing inquiry handoff.
 */

$selection   = function_exists( 'arctic_jucra_get_inquiry_selection' ) ? arctic_jucra_get_inquiry_selection( 'get' ) : array();
$is_valid    = !empty( $selection['valid'] );
$model_name  = isset( $selection['model_name'] ) ? (string)$selection['model_name'] : '';
$builder_url = isset( $selection['builder_url'] ) ? (string)$selection['builder_url'] : home_url( '/konfigurator/' );
$options     = isset( $selection['options'] ) && is_array( $selection['options'] ) ? $selection['options'] : array();
?>

<section class="f-section f-section--jucra-inquiry" data-jucra-inquiry>
	<div class="f-section__container a-container">
		<header class="f-jucra-inquiry__header">
			<div class="f-jucra-builder__breadcrumb" aria-label="<?php echo esc_attr__( 'Drobečková navigace', 'baspa' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr__( 'Úvod', 'baspa' ); ?>">⌂</a>
				<span aria-hidden="true">−</span>
				<a href="<?php echo esc_url( home_url( '/konfigurator/' ) ); ?>"><?php echo esc_html__( '3D konfigurátor', 'baspa' ); ?></a>
				<span aria-hidden="true">−</span>
				<span><?php echo esc_html__( 'Poptávka konfigurace', 'baspa' ); ?></span>
			</div>
			<h1><?php echo esc_html__( 'Dokončete poptávku konfigurace', 'baspa' ); ?></h1>
			<p><?php echo esc_html__( 'Zkontrolujte vybraný model a pošlete nám kontakt. Konfigurace se automaticky přiloží k poptávce.', 'baspa' ); ?></p>
		</header>

		<?php if ( $is_valid ) { ?>
			<div class="f-jucra-inquiry__grid">
				<aside class="f-jucra-inquiry__summary" aria-label="<?php echo esc_attr__( 'Souhrn konfigurace', 'baspa' ); ?>">
					<span class="f-jucra-inquiry__eyebrow"><?php echo esc_html__( 'Vybraná vířivka', 'baspa' ); ?></span>
					<h2><?php echo esc_html( $model_name ); ?></h2>

					<div class="f-jucra-inquiry__options">
						<?php foreach ( $options as $option ) {
							$value = isset( $option['value'] ) && is_array( $option['value'] ) ? $option['value'] : array();
							if ( empty( $value['label'] ) ) {
								continue;
							}
							?>
							<div class="f-jucra-inquiry__option">
								<?php if ( !empty( $value['icon_url'] ) ) { ?>
									<img src="<?php echo esc_url( $value['icon_url'] ); ?>" width="56" height="56" alt="" loading="lazy" decoding="async">
								<?php } else { ?>
									<span class="f-jucra-inquiry__option-dot" aria-hidden="true"></span>
								<?php } ?>
								<div>
									<strong><?php echo esc_html( $option['title'] ?? '' ); ?></strong>
									<span><?php echo esc_html( $value['label'] ); ?></span>
								</div>
							</div>
						<?php } ?>
					</div>

					<a class="a-button a-button--outline" href="<?php echo esc_url( $builder_url ); ?>">
						<?php echo esc_html__( 'Upravit konfiguraci', 'baspa' ); ?>
					</a>
				</aside>

				<div class="f-jucra-inquiry__form-card">
					<?php
					get_template_part( 'modules/contacts/templates/form-contact', null, array(
						'context'         => 'jucra',
						'header'          => true,
						'type'            => 'full',
						'jucra_selection' => $selection,
					) );
					?>
				</div>
			</div>
		<?php } else { ?>
			<div class="f-jucra-inquiry__missing">
				<strong><?php echo esc_html__( 'Chybí vybraná konfigurace.', 'baspa' ); ?></strong>
				<p><?php echo esc_html__( 'Vraťte se prosím do 3D konfigurátoru, vyberte model a varianty a potom znovu klikněte na “Vyžádat cenovou nabídku”.', 'baspa' ); ?></p>
				<a class="a-button a-button--accent" href="<?php echo esc_url( $builder_url ); ?>">
					<?php echo esc_html__( 'Zpět do konfigurátoru', 'baspa' ); ?>
				</a>
			</div>
		<?php } ?>
	</div>
</section>
