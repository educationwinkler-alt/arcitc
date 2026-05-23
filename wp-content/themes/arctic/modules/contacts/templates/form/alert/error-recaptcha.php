<?php

/**
 * ReCAPTCHA Error
 */

?>

<div class="f-alert f-alert--center a-alert--error a-alert--l f-form__alert js-form__alert" role="alert">
	<h5><?php echo esc_html__( 'We\'re sorry, but verification that you\'re not a robot failed.', 'baspa' ); ?></h5>
	<p><?php echo sprintf( __( 'You can try to submit the form again, and if it fails again, please write to our email address <a href="mailto:%s">%s</a>. We apologize for the inconvenience.', 'baspa' ), antispambot( get_theme_mod( 'baspa_email', esc_html__( 'info@arctic-spas.cz', 'baspa' ) ) ), antispambot( get_theme_mod( 'baspa_email', esc_html__( 'info@arctic-spas.cz', 'baspa' ) ) ) ); ?></p>
</div>
