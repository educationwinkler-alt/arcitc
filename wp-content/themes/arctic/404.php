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
                <h2><?php echo esc_html__( 'Tahle stránka už na nové adrese není.', 'baspa' ); ?></h2>
                <p><?php echo esc_html__( 'Pokračujte na úvod, do katalogu vířivek nebo nám napište, pokud hledáte konkrétní model či dokument.', 'baspa' ); ?></p>
                <a href="<?php echo esc_url( home_url() ); ?>" class="f-button a-button"><?php echo esc_html__( 'Zpět na úvod', 'baspa' ); ?></a>
            </div>
        </div>

    </main>

<?php
get_footer();
