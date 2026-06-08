<?php
/**
 * Arctic Jucra/Visao 3D builder.
 */

$selected_model = function_exists( 'arctic_jucra_get_selected_model' ) ? arctic_jucra_get_selected_model() : array(
	'slug'       => 'timberwolf',
	'label'      => 'Timberwolf',
	'model_name' => 'Timberwolf',
);
$models        = function_exists( 'arctic_jucra_model_definitions' ) ? arctic_jucra_model_definitions() : array();
$model_name    = $selected_model['model_name'] ?? '';
$show_viewer   = function_exists( 'arctic_jucra_can_render_viewer' ) && arctic_jucra_can_render_viewer( $model_name );
$shortcode     = $show_viewer && function_exists( 'arctic_jucra_build_shortcode' ) ? arctic_jucra_build_shortcode( $model_name ) : '';
$shortcode_tag = $show_viewer && function_exists( 'arctic_jucra_get_shortcode_tag' ) ? arctic_jucra_get_shortcode_tag() : 'visao_viewer';
$builder_image = content_url( 'uploads/import/figma/category-configurator.png' );
?>

<section class="f-section f-section--jucra-builder" data-jucra-builder data-content-source="jucra-settings" data-jucra-model="<?php echo esc_attr( $model_name ); ?>" data-model-count="<?php echo esc_attr( count( $models ) ); ?>">
	<div class="f-section__container a-container">
		<header class="f-jucra-builder__header">
			<div class="f-jucra-builder__breadcrumb" aria-label="<?php echo esc_attr__( 'Drobečková navigace', 'baspa' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr__( 'Úvod', 'baspa' ); ?>">⌂</a>
				<span aria-hidden="true">−</span>
				<span><?php echo esc_html__( '3D konfigurátor', 'baspa' ); ?></span>
			</div>
			<h1><?php echo esc_html__( 'Nakonfigurujte si vlastní vířivku', 'baspa' ); ?></h1>
			<p><?php echo esc_html__( 'Vyberte model, barvu skořepiny a kabinetu. Výběr nám pak pošlete jako poptávku.', 'baspa' ); ?></p>
		</header>

		<div class="f-jucra-builder__model-strip" aria-label="<?php echo esc_attr__( 'Výběr modelu', 'baspa' ); ?>">
			<?php foreach ( $models as $slug => $model ) {
				$is_active = ( $selected_model['slug'] ?? '' ) === $slug;
				$url       = function_exists( 'arctic_jucra_get_builder_url' ) ? arctic_jucra_get_builder_url( $model['model_name'] ?? '' ) : home_url( '/konfigurator/' );
				?>
				<a class="f-jucra-builder__model<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( $model['label'] ?? $slug ); ?>
				</a>
			<?php } ?>
		</div>

		<div class="f-jucra-builder__layout">
			<div class="f-jucra-builder__viewer">
				<div class="f-jucra-builder__viewer-surface">
					<?php if ( $show_viewer && $shortcode !== '' ) { ?>
						<div class="f-jucra-builder__shortcode" data-jucra-shortcode="<?php echo esc_attr( $shortcode_tag ); ?>">
							<?php
							echo function_exists( 'arctic_jucra_render_shortcode' )
								? arctic_jucra_render_shortcode( $shortcode, $model_name )
								: do_shortcode( $shortcode );
							?>
						</div>
					<?php } else { ?>
						<img src="<?php echo esc_url( $builder_image ); ?>" width="667" height="312" alt="" loading="eager" decoding="async">
						<div class="f-jucra-builder__waiting" data-jucra-status="WAITING_ON_JUCRA_PLUGIN">
							<strong><?php echo esc_html__( 'WAITING_ON_JUCRA_PLUGIN', 'baspa' ); ?></strong>
							<span><?php echo esc_html__( 'Plugin Visao 3d Viewer zatím není aktivní. Jakmile bude nainstalovaný, tady se vykreslí skutečný 3D viewer.', 'baspa' ); ?></span>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<?php if ( $show_viewer && $shortcode !== '' ) { ?>
			<script>
				(function () {
					const script = document.currentScript;
					const movePricingCta = function () {
						const root = script.closest('.f-section--jucra-builder');
						const options = root && root.querySelector('#visao-options-container');
						const pricing = root && root.querySelector('#visao-pricing-cta-container');

						if (options && pricing && !options.contains(pricing)) {
							options.appendChild(pricing);
						}
					};

					movePricingCta();
					document.addEventListener('DOMContentLoaded', movePricingCta);
					window.addEventListener('load', movePricingCta);
					window.setTimeout(movePricingCta, 1200);
				})();
			</script>
		<?php } ?>
	</div>
</section>
