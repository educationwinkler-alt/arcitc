<?php

/**
 * Post Listing Meta
 */

$reference_location = get_post_meta( get_the_ID(), 'reference_location', true );
$reference_year      = get_post_meta( get_the_ID(), 'reference_year', true );

if ( !empty( $reference_location ) || !empty( $reference_year ) ) { ?>
	<div class="f-listing__metas f-metas">
		<div class="a-container">
			<ul>
				<?php if ( !empty( $reference_location ) ) { ?>
					<li class="f-meta">
						<span class="f-meta__title screen-reader-text"><?php echo esc_html__( 'Location', 'baspa' ); ?></span>
						<span class="f-meta__value"><?php echo esc_html( $reference_location ); ?></span>
					</li>
				<?php } ?>
				<?php if ( !empty( $reference_year ) ) { ?>
					<li class="f-meta">
						<span class="f-meta__title screen-reader-text"><?php echo esc_html__( 'Year', 'baspa' ); ?></span>
						<span class="f-meta__value"><?php echo esc_html( $reference_year ); ?></span>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
<?php }
