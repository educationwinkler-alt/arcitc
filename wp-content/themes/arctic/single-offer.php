<?php

/**
 * Single Offer
 */

get_header();
get_template_part( 'modules/offers/templates/post/single/heading' );
?>

    <main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
          class="f-main f-main--single f-main--single-offer f-main--top-0">

		<?php
		get_template_part( 'modules/offers/templates/post/single' );
		get_template_part( 'modules/references/templates/section', 'recent' );
		?>

    </main>

<?php
get_footer();
