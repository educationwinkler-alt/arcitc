<?php

/**
 * Template Name: Blog
 */

get_header();
get_template_part( 'templates/heading' );
?>

    <main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
          class="f-main f-main--blog">

        <div class="a-container">
			<?php
			get_template_part( 'modules/posts/templates/loop/blog' );
			?>
        </div>

    </main>

<?php
get_footer();
