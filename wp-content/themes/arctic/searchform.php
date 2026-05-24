<?php

/**
 * Search Form
 */

$search_id = get_query_var( 'baspa_search_id' ) ?: 'search';

?>

<form role="search" method="get" class="f-search js-search" action="<?php echo esc_url( home_url( '/' ) ); ?>"
      data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">

	<input type="hidden" name="post_type" value="post,page,product">
	<input type="hidden" name="post_taxonomy" value="category,product-category">

	<div class="f-search__field f-field--search f-field a-field">
		<?php if ( function_exists( 'forqy_get_icon' ) ) { ?>
			<div class="f-search__icon f-field__icon">
				<?php forqy_get_icon( 'search' ); ?>
			</div>
		<?php } ?>

		<label for="<?php echo esc_attr( $search_id ); ?>" class="f-label a-label screen-reader-text">
			<?php echo esc_html_x( 'Vyhledat', 'label', 'baspa' ); ?>
		</label>

		<input type="search" id="<?php echo esc_attr( $search_id ); ?>" name="s" class="f-search__input f-input a-input"
		       placeholder="<?php echo esc_attr_x( 'Zadejte hledaný výraz', 'placeholder', 'baspa' ); ?>"
		       value="<?php echo get_search_query(); ?>" required>

		<button type="submit" class="f-search__button f-button a-button a-button--outline">
			<?php echo esc_html_x( 'Vyhledat', 'submit button', 'baspa' ); ?>
		</button>
	</div>

	<div class="f-search__results js-search__results"></div>
</form>
