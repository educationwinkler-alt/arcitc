<?php

/**
 * Section Template
 */

// Query
$members_query = baspa_members_query_contacts();

if ( $members_query->have_posts() ) { ?>

	<section id="<?php echo sanitize_title( esc_attr_x( 'team', 'anchor', 'baspa' ) ); ?>"
	         class="f-section f-section--team">

		<div class="f-section__container a-container">

			<div class="f-section__heading a-stack a-gap--s">
				<header class="f-section__header">
					<h2><?php echo wp_kses_post( __( 'Important Contacts', 'baspa' ) ); ?></h2>
				</header>
			</div>

			<?php
			get_template_part( 'templates/loop', '', array(
				'query_args'           => $members_query->query,
				'query_module'         => 'members',
				'query_class'          => array( 'f-listings', 'a-grid', 'a-gap--xxs', 'a-gap--xs:m' ),
				'query_pagination'     => false,
				'query_posts_per_page' => -1,
				'query_listing'        => 'card',
			) );
			?>

		</div>

	</section>

<?php }
