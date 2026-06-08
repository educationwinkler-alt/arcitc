<?php

/**
 * Footer
 *
 * @since 1.0.0
 */

if ( !function_exists( 'arctic_footer_menu_groups' ) ) {

	/**
	 * Convert the WP footer menu into the Figma footer column model.
	 *
	 * @param array $fallback
	 *
	 * @return array
	 */
	function arctic_footer_menu_groups( array $fallback = array() ): array {

		$locations = get_nav_menu_locations();
		$menu_id   = !empty( $locations['navigation_footer'] ) ? (int) $locations['navigation_footer'] : 0;

		if ( !$menu_id ) {
			return $fallback;
		}

		$items = wp_get_nav_menu_items( $menu_id, array(
			'update_post_term_cache' => false,
		) );

		if ( empty( $items ) || !is_array( $items ) ) {
			return $fallback;
		}

		$children = array();
		foreach ( $items as $item ) {
			$parent_id                = (int) $item->menu_item_parent;
			$children[ $parent_id ][] = $item;
		}

		$link = static function ( WP_Post $item ): array {
			return array(
				'label' => $item->title,
				'url'   => $item->url,
			);
		};

		$groups = array();
		foreach ( $children[0] ?? array() as $top_item ) {
			$group = array(
				'heading'  => $top_item->title,
				'items'    => array(),
				'sections' => array(),
				'source'   => 'wp-menu',
			);

			foreach ( $children[ (int) $top_item->ID ] ?? array() as $child_item ) {
				$grandchildren = $children[ (int) $child_item->ID ] ?? array();

				if ( !empty( $grandchildren ) ) {
					$section = array(
						'heading' => $child_item->title,
						'items'   => array(),
					);

					foreach ( $grandchildren as $grandchild_item ) {
						$section['items'][] = $link( $grandchild_item );
					}

					$group['sections'][] = $section;
				} else {
					$group['items'][] = $link( $child_item );
				}
			}

			$groups[] = $group;
		}

		return !empty( $groups ) ? $groups : $fallback;
	}

}

$contact_member = function_exists( 'baspa_members_get_selected_contact' ) ? baspa_members_get_selected_contact( 'footer_quick', get_template() . '-avatar' ) : array();
$phone          = !empty( $contact_member['phone'] ) ? $contact_member['phone'] : get_theme_mod( 'baspa_phone', '+420 777 099 687' );
$email          = !empty( $contact_member['email'] ) ? $contact_member['email'] : get_theme_mod( 'baspa_email', 'info@arctic-spas.cz' );
$fallback_name  = trim( (string) get_theme_mod( 'baspa_name', get_bloginfo( 'name' ) ) );
$contact_name   = !empty( $contact_member['name'] ) ? $contact_member['name'] : ( $fallback_name !== '' ? $fallback_name : __( 'Arctic Spas', 'baspa' ) );
$contact_role   = !empty( $contact_member['position'] ) ? $contact_member['position'] : __( 'Rychly kontakt', 'baspa' );
$contact_source = !empty( $contact_member['source'] ) ? $contact_member['source'] : 'customizer-about';
$fallback_initial = function_exists( 'mb_substr' ) ? mb_substr( $contact_name, 0, 1 ) : substr( $contact_name, 0, 1 );
$fallback_initial = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $fallback_initial ) : strtoupper( $fallback_initial );
$avatar_member  = !empty( $contact_member ) ? $contact_member : array(
	'name'         => $contact_name,
	'initials'     => function_exists( 'baspa_member_initials' ) ? baspa_member_initials( $contact_name ) : $fallback_initial,
	'asset_status' => 'admin-empty',
);
$hours          = apply_filters( 'forqy_hours', array() );
$hours_label = function_exists( 'baspa_hours_bar_label' ) ? baspa_hours_bar_label( $hours ) : __( 'Po - Pá 8:00-17:00 h', 'baspa' );
$map_url = function_exists( 'arctic_get_map_url' ) ? arctic_get_map_url() : get_theme_mod( 'baspa_map', 'https://maps.app.goo.gl/ZsYfoZ2aQGF1JnZG6' );
$street  = get_theme_mod( 'baspa_street', 'Bohunická cesta 15' );
$city    = get_theme_mod( 'baspa_city', 'Moravany u Brna' );
$company_name = trim( (string) get_theme_mod( 'baspa_name', get_bloginfo( 'name' ) ) );
$copyright_setting = trim( (string) get_theme_mod( 'baspa_copyright', '' ) );
$copyright_text = $copyright_setting !== ''
	? $copyright_setting
	: sprintf(
		'Copyright © %s %s. Všechna práva vyhrazena.',
		date_i18n( 'Y' ),
		$company_name !== '' ? $company_name : get_bloginfo( 'name' )
	);
$copyright_source = $copyright_setting !== '' ? 'customizer-about' : 'computed-fallback';

$footer_fallback_groups = array(
	array(
		'heading' => __( 'Vířivky', 'baspa' ),
		'items'   => array(
			__( 'Série Core', 'baspa' )      => home_url( '/virivky/?series=core' ),
			__( 'Série Classic', 'baspa' )   => home_url( '/virivky/?series=classic' ),
			__( 'Série Custom', 'baspa' )    => home_url( '/virivky/?series=custom' ),
		),
		'sections' => array(
			array(
				'heading' => __( 'Celoroční bazény', 'baspa' ),
				'items'   => array(
					__( 'Bazény ARCTIC Classic', 'baspa' ) => home_url( '/swimspa/#serie-swimspa-classic' ),
					__( 'Bazény ARCTIC Custom', 'baspa' )  => home_url( '/swimspa/#serie-swimspa-custom' ),
				),
			),
		),
	),
	array(
		'heading' => __( 'Vlastnosti vířivek', 'baspa' ),
		'items'   => array(
			__( 'Izolace vířivky', 'baspa' )        => home_url( '/vlastnosti/izolace-virivky/' ),
			__( 'Záruka na skořepinu', 'baspa' )    => home_url( '/vlastnosti/zaruka-na-skorepinu/' ),
			__( 'Termokryt', 'baspa' )              => home_url( '/vlastnosti/termokryt/' ),
			__( 'Podlaha vířivky', 'baspa' )        => home_url( '/vlastnosti/podlaha-virivky/' ),
			__( 'Servisní přístup', 'baspa' )       => home_url( '/vlastnosti/servisni-pristup/' ),
			__( 'Variabilita', 'baspa' )            => home_url( '/vlastnosti/variabilita/' ),
			__( 'Automatická dezinfekce', 'baspa' ) => home_url( '/vlastnosti/automaticka-dezinfekce/' ),
		),
	),
	array(
		'heading' => __( 'Další informace', 'baspa' ),
		'items'   => array(
			__( 'Průběh realizace', 'baspa' )             => home_url( '/#order-progress' ),
			__( 'Podpora', 'baspa' )                      => home_url( '/podpora/' ),
			__( 'Služby', 'baspa' )                       => home_url( '/sluzby/' ),
			__( 'Akční nabídky', 'baspa' )                => function_exists( 'baspa_offers_archive_url' ) ? baspa_offers_archive_url() : home_url( '/akcni-nabidky/' ),
			__( 'Kolik stojí provoz a údržba', 'baspa' )  => home_url( '/kolik-stoji-udrzba/' ),
			__( 'Časté otázky', 'baspa' )                 => home_url( '/podpora/#caste-dotazy' ),
			__( 'Reference', 'baspa' )                    => home_url( '/reference/' ),
			__( 'O nás', 'baspa' )                        => home_url( '/o-nas/' ),
			__( 'Showroom', 'baspa' )                     => home_url( '/showroom/' ),
			__( 'Servis', 'baspa' )                       => home_url( '/servis/' ),
			__( 'Kontakt', 'baspa' )                      => home_url( '/kontakt/' ),
		),
	),
);

$groups = arctic_footer_menu_groups( $footer_fallback_groups );
?>

<footer id="<?php echo sanitize_title( esc_attr_x( 'footer', 'anchor', 'baspa' ) ); ?>" class="f-footer f-footer--arctic">
	<div class="f-footer__container a-container">
		<div class="f-footer__grid">
			<?php foreach ( $groups as $group ) { ?>
				<nav class="f-footer__group" aria-label="<?php echo esc_attr( $group['heading'] ); ?>" data-content-source="<?php echo esc_attr( $group['source'] ?? 'php-fallback' ); ?>">
					<h2><?php echo esc_html( $group['heading'] ); ?></h2>
					<ul>
						<?php foreach ( $group['items'] as $fallback_label => $item ) {
							$label = is_array( $item ) ? ( $item['label'] ?? '' ) : ( is_string( $fallback_label ) ? $fallback_label : (string) $item );
							$url   = is_array( $item ) ? ( $item['url'] ?? '' ) : ( is_string( $item ) ? $item : '#' ); ?>
							<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
						<?php } ?>
					</ul>
					<?php if ( !empty( $group['sections'] ) ) {
						foreach ( $group['sections'] as $section ) { ?>
							<h2><?php echo esc_html( $section['heading'] ); ?></h2>
							<ul>
								<?php foreach ( $section['items'] as $fallback_label => $item ) {
									$label = is_array( $item ) ? ( $item['label'] ?? '' ) : ( is_string( $fallback_label ) ? $fallback_label : (string) $item );
									$url   = is_array( $item ) ? ( $item['url'] ?? '' ) : ( is_string( $item ) ? $item : '#' ); ?>
									<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
								<?php } ?>
							</ul>
						<?php }
					} ?>
				</nav>
			<?php } ?>

			<aside class="f-footer__quick-contact"
			       <?php echo !empty( $contact_member['id'] ) ? 'data-member-id="' . esc_attr( (string) $contact_member['id'] ) . '"' : ''; ?>
			       data-content-source="<?php echo esc_attr( $contact_source ); ?>">
				<div class="f-footer__quick-contact-body">
					<h2><?php echo esc_html__( 'Rychlý kontakt', 'baspa' ); ?></h2>
					<div class="f-footer__quick-person">
						<?php if ( function_exists( 'baspa_member_avatar_html' ) ) {
							echo baspa_member_avatar_html( $avatar_member, 'f-footer__quick-avatar', get_template() . '-avatar' );
						} else { ?>
							<span class="f-footer__quick-avatar f-footer__quick-avatar--waiting" data-asset-status="admin-empty" aria-hidden="true"><?php echo esc_html( $fallback_initial ); ?></span>
						<?php } ?>
						<div>
							<strong><?php echo esc_html( $contact_name ); ?></strong>
							<span><?php echo esc_html( $contact_role ); ?></span>
						</div>
					</div>
					<a href="mailto:<?php echo antispambot( esc_attr( $email ) ); ?>"><?php echo antispambot( esc_html( $email ) ); ?></a>
					<a href="tel:<?php echo esc_attr( function_exists( 'baspa_member_phone_href' ) ? baspa_member_phone_href( $phone ) : str_replace( ' ', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<?php get_template_part( 'templates/about/hours', '', array(
						'class' => array( 'f-footer__quick-hours' ),
						'label' => $hours_label,
					) ); ?>
					<?php get_template_part( 'templates/button/contact', '', array(
						'text' => __( 'Nezávazná konzultace', 'baspa' ),
					) ); ?>
				</div>
				<div class="f-footer__quick-map">
					<div class="f-footer__quick-map-address">
						<span><?php echo esc_html( $street ); ?></span>
						<strong><?php echo esc_html( $city ); ?></strong>
					</div>
					<a href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html__( 'Zobrazit na mapě', 'baspa' ); ?></a>
				</div>
			</aside>
		</div>

		<div class="f-footer__bottom">
			<div class="f-footer__copyright" data-content-source="<?php echo esc_attr( $copyright_source ); ?>">
				<?php echo esc_html( $copyright_text ); ?>
			</div>
			<?php get_template_part( 'templates/logo' ); ?>
			<a href="<?php echo esc_url( home_url( '/ochrana-osobnich-udaju/' ) ); ?>"><?php echo esc_html__( 'Ochrana osobních údajů', 'baspa' ); ?></a>
		</div>
	</div>
</footer>
