<?php

/**
 * 404
 */

get_header();
get_template_part( 'templates/heading/404' );
?>

    <main id="<?php echo sanitize_title( esc_attr_x( 'content', 'anchor', 'baspa' ) ); ?>"
          class="f-main f-main--404">

        <div class="a-container">
            <div class="f-content a-content">
                <h2><?php echo esc_html__( 'It looks like nothing was found at this location.', 'baspa' ); ?></h2>
                <a href="<?php echo esc_url( home_url() ); ?>" class="f-button a-button"><?php echo esc_html__( 'Back to Homepage', 'baspa' ); ?></a>
            </div>
        </div>

    </main>

<?php
get_footer();
