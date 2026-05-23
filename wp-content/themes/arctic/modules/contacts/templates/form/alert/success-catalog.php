<?php

/**
 * Success
 */

?>

<div class="f-alert f-alert--center a-alert--success a-alert--l f-form__alert js-form__alert" role="alert">
	<h5><?php echo esc_html__( 'Formulář byl úspěšně odeslán. Děkujeme.', 'baspa' ); ?></h5>
	<p>
		<?php echo wp_kses_post( sprintf( 'Katalog je nyn&iacute; na cest&#283; do va&scaron;&iacute; e-mailov&eacute; schr&aacute;nky. V&scaron;echny dostupn&eacute; materi&aacute;ly si v&scaron;ak m&#367;&#382;ete ihned st&aacute;hnout <a href="%s">zde</a>.', esc_url( home_url( '/ke-stazeni/' ) ) ) ); ?>
	</p>
</div>
