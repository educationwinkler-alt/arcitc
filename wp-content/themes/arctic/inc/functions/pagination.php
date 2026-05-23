<?php

/**
 * Pagination
 */

if ( !function_exists( 'baspa_pagination' ) ) {

	/**
	 * Pagination - Post
	 *
	 * @param int|null $pages
	 * @param int|null $paged
	 * @param int|null $page_range
	 *
	 * @return void
	 */
	function baspa_pagination( int $pages = null, int $paged = null, int $page_range = null ): void {

		if ( function_exists( 'forqy_pagination' ) && $pages > 1 ) {

			ob_start();
			get_template_part( 'images/icon/arrow-left', 'xs' );
			$prev = ob_get_clean() . '<span class="screen-reader-text">' . esc_html__( 'Previous', 'baspa' ) . '</span>';

			ob_start();
			get_template_part( 'images/icon/arrow-right', 'xs' );
			$next = ob_get_clean() . '<span class="screen-reader-text">' . esc_html__( 'Next', 'baspa' ) . '</span>';
			?>

			<nav class="f-pagination" itemscope itemtype="https://schema.org/SiteNavigationElement"
			     aria-label="<?php echo esc_attr__( 'Pagination', 'baspa' ); ?>">

				<?php forqy_pagination( array(
					'prev_text' => $prev,
					'next_text' => $next,
				), $pages, $paged, $page_range ); ?>
			</nav>

		<?php }

	}

}

if ( !function_exists( 'baspa_pagination_post' ) ) {

	/**
	 * Pagination - Post
	 *
	 * @return void
	 */
	function baspa_pagination_post(): void {

		if ( function_exists( 'forqy_pagination_post' ) ) { ?>

			<nav class="f-pagination f-pagination--post" itemscope itemtype="https://schema.org/SiteNavigationElement"
			     aria-label="<?php echo esc_attr__( 'Pagination', 'baspa' ); ?>">

				<?php forqy_pagination_post( array(
					'prev_text' => esc_html__( 'Previous', 'baspa' ),
					'next_text' => esc_html__( 'Next', 'baspa' ),
				) ); ?>
			</nav>

		<?php }

	}

}
