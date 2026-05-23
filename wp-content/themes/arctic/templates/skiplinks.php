<?php

/**
 * Skip Links
 *
 * @since 1.0.0
 */

?>

<a href="#<?php echo sanitize_title( esc_attr_x( 'navigation', 'anchor', 'baspa' ) ); ?>"
   class="f-skiplink a-skiplink"><?php echo esc_html_x( 'Skip to navigation', 'skip link', 'baspa' ); ?></a>
<a href="#<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
   class="f-skiplink a-skiplink"><?php echo esc_html_x( 'Skip to content', 'skip link', 'baspa' ); ?></a>
<a href="#<?php echo sanitize_title( esc_attr_x( 'footer', 'anchor', 'baspa' ) ); ?>"
   class="f-skiplink a-skiplink"><?php echo esc_html_x( 'Skip to footer', 'skip link', 'baspa' ); ?></a>
