<html>
<head>
	<?php require_once get_theme_file_path( 'modules/contacts/templates/email/_styles.php' ); ?>
</head>
<body>
<table>
	<tr>
		<td>
			<?php if ( !empty( $content ) ) { ?>
				<p><?php echo $content; ?></p>
				<br>
			<?php } ?>

			<table>
				<?php if ( !empty( $name ) ) { ?>
					<tr>
						<th><?php echo esc_html__( 'Name', 'baspa' ); ?>:</th>
						<td><?php echo esc_html( $name ); ?></td>
					</tr>
				<?php } ?>
				<?php if ( !empty( $email ) ) { ?>
					<tr>
						<th><?php echo esc_html__( 'Email', 'baspa' ); ?>:</th>
						<td><?php echo esc_html( $email ); ?></td>
					</tr>
				<?php } ?>
				<?php if ( !empty( $phone ) ) { ?>
					<tr>
						<th><?php echo esc_html__( 'Phone', 'baspa' ); ?>:</th>
						<td><?php echo esc_html( $phone ); ?></td>
					</tr>
				<?php } ?>
				<?php if ( !empty( $interest ) ) { ?>
					<tr>
						<th><?php echo esc_html__( 'Interest', 'baspa' ); ?>:</th>
						<td><?php echo esc_html( baspa_contacts_get_interest_title( $interest ) ); ?></td>
					</tr>
				<?php } ?>
				<?php if ( !empty( $jucra_config['model_name'] ) ) { ?>
					<tr>
						<th><?php echo esc_html__( '3D konfigurace', 'baspa' ); ?>:</th>
						<td>
							<strong><?php echo esc_html( $jucra_config['model_name'] ); ?></strong>
							<?php foreach ( $jucra_config['options'] as $option ) {
								if ( empty( $option['label'] ) ) {
									continue;
								}
								?>
								<br><?php echo esc_html( $option['title'] ?? '' ); ?>: <?php echo esc_html( $option['label'] ); ?>
							<?php } ?>
							<?php if ( !empty( $jucra_config['builder_url'] ) ) { ?>
								<br><a href="<?php echo esc_url( $jucra_config['builder_url'] ); ?>"><?php echo esc_html__( 'Otevřít model v konfigurátoru', 'baspa' ); ?></a>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				<?php if ( !empty( $message ) ) { ?>
					<tr>
						<th><?php echo esc_html__( 'Message', 'baspa' ); ?>:</th>
						<td><?php echo esc_html( $message ); ?></td>
					</tr>
				<?php } ?>
			</table>

		</td>
	</tr>
</table>
</body>
</html>
