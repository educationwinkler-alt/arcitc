<?php
/**
 * Template Name: Figma Servis
 */

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--service-request">
	<section class="f-section f-section--service-request">
		<div class="f-section__container a-container">
			<div class="f-service-request__form">
				<?php
				get_template_part( 'modules/contacts/templates/form', 'service', array(
					'header' => false,
					'type'   => 'full',
				) );
				?>
			</div>
			<div class="f-service-request__pricing f-service-request__pricing--warranty">
				<h2><?php echo esc_html__( 'Záruční servis', 'baspa' ); ?></h2>
				<p><?php echo esc_html__( 'Zdarma do dvou let včetně dopravného. V případě prodloužených záruk poskytovaných výrobcem platí zákazník od třetího roku dopravu 17 Kč/km.', 'baspa' ); ?></p>
			</div>
			<div class="f-service-request__pricing f-service-request__pricing--paid">
				<h2><?php echo esc_html__( 'Pozáruční servis', 'baspa' ); ?></h2>
				<ul>
					<li><?php echo esc_html__( 'Dopravné: 17 Kč/km', 'baspa' ); ?></li>
					<li><?php echo esc_html__( 'Práce technika: 900 Kč/hod', 'baspa' ); ?></li>
					<li><?php echo esc_html__( 'Zazimování vířivky: 2.400 Kč + dopravné', 'baspa' ); ?></li>
					<li><?php echo esc_html__( 'Zprovoznění vířivky: 2.400 Kč + dopravné', 'baspa' ); ?></li>
				</ul>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
