<?php

/**
 * Error
 */

?>

<div class="f-alert f-alert--center a-alert--error a-alert--l f-form__alert js-form__alert" role="alert">
	<h5><?php echo esc_html__( 'Omlouváme se, odeslání formuláře se nezdařilo.', 'baspa' ); ?></h5>
	<p><?php echo sprintf( __( 'Zkuste formulář odeslat znovu. Pokud se chyba zopakuje, napište nám prosím na <a href="mailto:%s">%s</a>.', 'baspa' ), antispambot( get_theme_mod( 'baspa_email', esc_html__( 'info@arctic-spas.cz', 'baspa' ) ) ), antispambot( get_theme_mod( 'baspa_email', esc_html__( 'info@arctic-spas.cz', 'baspa' ) ) ) ); ?></p>
</div>
