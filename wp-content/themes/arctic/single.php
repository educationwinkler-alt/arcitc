<?php

/**
 * Single
 */

get_header();
?>

    <main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
          class="f-main f-main--single f-main--single-post f-main--top-0">

		<?php
		get_template_part( 'modules/posts/templates/post/single' );
		?>

    </main>

<?php
get_template_part( 'modules/posts/templates/section', 'related' );
get_template_part( 'templates/section/showroom' );
get_footer();
