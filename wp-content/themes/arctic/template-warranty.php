<?php
/**
 * Template Name: Figma Záruka
 */

$rows = array(
	array( 'Skořepina', 'Doživotní', '10 let', '7 let' ),
	array( 'Akrylát', '5 let', '4 roky', '1 rok' ),
	array( 'Podlaha', 'Doživotní', '3 roky', '3 roky' ),
	array( 'Náhradní díly', '5 let', '3 roky', '3 roky' ),
	array( 'Práce', '5 let', '3 roky', '1 rok' ),
);

get_header();
get_template_part( 'templates/heading' );
?>

<main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>" class="f-main f-main--figma-page f-main--warranty">
	<section class="f-section f-section--warranty-table">
		<div class="f-section__container a-container">
			<div class="f-warranty-layout">
				<table class="f-warranty-table">
					<thead>
					<tr>
						<th></th>
						<th><?php echo esc_html__( 'Custom', 'baspa' ); ?></th>
						<th><?php echo esc_html__( 'Classic', 'baspa' ); ?></th>
						<th><?php echo esc_html__( 'Core', 'baspa' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $rows as $row ) { ?>
						<tr>
							<?php foreach ( $row as $cell ) { ?>
								<td><?php echo esc_html( $cell ); ?></td>
							<?php } ?>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<p><?php echo esc_html__( 'Dopravné: první dva roky záruky hradí cestu servisního technika prodávající a od třetího roku tuto platí zákazník, ať jde o opravu dílů spadajících do prodloužené záruky, nebo o pozáruční servis. Konkrétní záruční podmínky naleznete v uživatelském manuálu v sekci Ke stažení.', 'baspa' ); ?></p>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
