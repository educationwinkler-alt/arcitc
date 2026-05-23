<?php

/**
 * Email
 */

/**
 * Email From Address
 */
//add_filter( 'wp_mail_from', function () {
//	return esc_html__( 'noreply@baspa.com', 'baspa' );
//} );

/**
 * Email From Name
 */
//add_filter( 'wp_mail_from_name', function () {
//	return esc_html__( 'Baspa', 'baspa' );
//} );

/**
 * Set Return-Path the Same as From Address
 */
add_action( 'phpmailer_init', function ( $phpmailer ) {
	$phpmailer->Sender = $phpmailer->From;
} );
