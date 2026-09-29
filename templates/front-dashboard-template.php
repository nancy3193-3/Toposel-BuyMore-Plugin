<?php
// dashboard html - variables ($settings, $user, $banners, $explore etc)
// come from toposel_dashboard_shortcode()

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// pick one banner per grid slot (hero / medium / small / wide)
$pick_banner = function ( $size ) use ( &$banners ) {
	foreach ( $banners as $i => $b ) {
		if ( $b['size'] === $size ) {
			unset( $banners[ $i ] );
			return $b;
		}
	}
	return null;
};

$banner_large  = $pick_banner( 'large' );
$banner_medium = $pick_banner( 'medium' );
$banner_small  = $pick_banner( 'small' );
$banner_wide   = $pick_banner( 'wide' );
$spotlights    = array_slice( $explore, 0, 2 );

// ---- sidebar nav menu build (layout + icons + items, all here) ----

// icon per menu item, matched against the item's "CSS Classes" field
$nav_icons = array(
	'icon-popular'     => '<svg viewBox="0 0 20 20" fill="none"><path d="M10 2l2.2 5.6L18 9l-4.6 3.8L14.8 18 10 14.8 5.2 18l1.4-5.2L2 9l5.8-1.4L10 2z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>',
	'icon-explore'     => '<svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.4"/><path d="M13 7l-2 4-4 2 2-4 4-2z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>',
	'icon-clothing'    => '<svg viewBox="0 0 20 20" fill="none"><path d="M7 3l3 2 3-2 3 3-2 2v9H6V8L4 6l3-3z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>',
	'icon-gifts'       => '<svg viewBox="0 0 20 20" fill="none"><rect x="3" y="8" width="14" height="9" rx="1" stroke="currentColor" stroke-width="1.4"/><path d="M3 8h14M10 8v9M10 8C8 4 4 5 5 7c1 1.5 3 1 5 1zM10 8c2-4 6-3 5-1-1 1.5-3 1-5 1z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>',
	'icon-inspiration' => '<svg viewBox="0 0 20 20" fill="none"><path d="M10 2a5 5 0 00-3 9c.6.5 1 1.2 1 2h4c0-.8.4-1.5 1-2a5 5 0 00-3-9z" stroke="currentColor" stroke-width="1.4"/><path d="M8.5 16h3M9 18h2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>',
);

// pull the "BuyMore Sidebar Navigation" menu items and turn them into a
// plain list the markup below can loop over (title, url, icon, active state)
$nav_items      = array();
$nav_locations  = get_nav_menu_locations();
$nav_menu_items = ! empty( $nav_locations['toposel_sidebar'] ) ? wp_get_nav_menu_items( $nav_locations['toposel_sidebar'] ) : array();

if ( $nav_menu_items ) {
	foreach ( $nav_menu_items as $menu_item ) {
		$item_classes = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes;

		$icon = '';
		foreach ( $nav_icons as $slug => $svg ) {
			if ( in_array( $slug, $item_classes, true ) ) {
				$icon = $svg;
				break;
			}
		}

		$nav_items[] = array(
			'title'     => $menu_item->title,
			'url'       => $menu_item->url,
			'icon'      => $icon,
			'is_active' => in_array( 'current-menu-item', $item_classes, true ) || in_array( 'is-default-active', $item_classes, true ),
		);
	}
}
?>
<div class="alignfull">
<div class="buymore">
	<div class="buymore__shell">

		<!-- sidebar: logo, nav menu, quick actions, last orders -->
		<aside class="buymore-sidebar">
			<div class="buymore-sidebar__logo">
				<span class="buymore-sidebar__dot"></span>
				<?php echo esc_html( $settings['store_name'] ); ?>
			</div>

			<nav class="buymore-nav" aria-label="<?php esc_attr_e( 'Primary', 'toposel-assignment' ); ?>">
				<ul class="buymore-nav__list">
					<?php foreach ( $nav_items as $nav_item ) : ?>
					<li class="buymore-nav__item<?php echo $nav_item['is_active'] ? ' is-active' : ''; ?>">
						<a href="<?php echo esc_url( $nav_item['url'] ); ?>" class="buymore-nav__link">
							<?php if ( $nav_item['icon'] ) : ?>
								<span class="buymore-nav__icon" aria-hidden="true"><?php echo $nav_item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed inline SVG set from $nav_icons above, not user input. ?></span>
							<?php endif; ?>
							<span class="buymore-nav__label"><?php echo esc_html( $nav_item['title'] ); ?></span>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="buymore-sidebar__section">
				<h4 class="buymore-sidebar__heading"><?php esc_html_e( 'Quick actions', 'toposel-assignment' ); ?></h4>
				<ul class="buymore-quicklist">
					<li><span class="buymore-quicklist__icon">+</span><?php esc_html_e( 'Request for product', 'toposel-assignment' ); ?></li>
					<li><span class="buymore-quicklist__icon">+</span><?php esc_html_e( 'Add member', 'toposel-assignment' ); ?></li>
				</ul>
			</div>

			<?php if ( ! empty( $orders ) ) : ?>
			<div class="buymore-sidebar__section">
				<h4 class="buymore-sidebar__heading"><?php esc_html_e( 'Last orders', 'toposel-assignment' ); ?> <span class="buymore-sidebar__badge"><?php echo esc_html( $settings['orders_count'] ); ?></span></h4>
				<ul class="buymore-orderlist">
					<?php foreach ( $orders as $order ) : ?>
					<li class="buymore-orderlist__item">
						<span class="buymore-orderlist__thumb" <?php if ( $order['image'] ) : ?>style="background-image:url(<?php echo esc_url( $order['image'] ); ?>)"<?php endif; ?>></span>
						<span class="buymore-orderlist__text">
							<strong><?php echo esc_html( $order['title'] ); ?></strong>
							<a href="#"><?php echo esc_html( $order['status'] ); ?></a>
						</span>
					</li>
					<?php endforeach; ?>
				</ul>
				<a href="#" class="buymore-sidebar__seeall"><?php esc_html_e( 'See all', 'toposel-assignment' ); ?></a>
			</div>
			<?php endif; ?>

			<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="buymore-sidebar__logout">
				<span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Log out', 'toposel-assignment' ); ?>
			</a>
		</aside>

		<main class="buymore-main">

			<!-- header: orders stat, dashboard/website toggle, cart + user -->
			<header class="buymore-header">
				<div class="buymore-header__stat">
					<strong><?php echo esc_html( $settings['orders_count'] ); ?></strong>
					<span>
						<?php esc_html_e( 'Orders', 'toposel-assignment' ); ?><br>
						<?php echo esc_html( $settings['orders_period'] ); ?>
					</span>
				</div>

				<div class="buymore-header__toggle" role="tablist">
					<button type="button" class="is-active" role="tab" aria-selected="true"><?php esc_html_e( 'Dashboard', 'toposel-assignment' ); ?></button>
					<button type="button" role="tab" aria-selected="false"><?php esc_html_e( 'Website', 'toposel-assignment' ); ?></button>
				</div>

				<div class="buymore-header__actions">
					<button type="button" class="buymore-cart-btn">
						<svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3 4h2l1.6 9.2a2 2 0 002 1.8h6.4a2 2 0 002-1.7L18 7H6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="8.5" cy="17" r="1" fill="currentColor"/><circle cx="15" cy="17" r="1" fill="currentColor"/></svg>
						<?php esc_html_e( 'Cart', 'toposel-assignment' ); ?>
						<?php if ( ! empty( $settings['cart_count'] ) ) : ?>
							<span class="buymore-icon-btn__badge"><?php echo esc_html( $settings['cart_count'] ); ?></span>
						<?php endif; ?>
					</button>

					<div class="buymore-header__user">
						<?php if ( ! empty( $user['avatar'] ) ) : ?>
							<img src="<?php echo esc_url( $user['avatar'] ); ?>" alt="" class="buymore-header__avatar">
						<?php else : ?>
							<span class="buymore-header__avatar buymore-header__avatar--fallback"><?php echo esc_html( mb_substr( $user['name'], 0, 1 ) ); ?></span>
						<?php endif; ?>
						<span class="buymore-header__username"><?php echo esc_html( $user['name'] ); ?></span>
					</div>
				</div>
			</header>

			<!-- explore section: heading, filter tabs, product grid -->
			<section class="buymore-explore">
				<div class="buymore-explore__top">
					<h1 class="buymore-explore__title"><?php echo esc_html( $settings['explore_intro'] ); ?></h1>

					<div class="buymore-filters" role="tablist">
    <button type="button" class="is-active" role="tab">
        <span class="filter-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
                <path d="M6 8h12l1 12H5L6 8z"/>
                <path d="M9 8a3 3 0 0 1 6 0"/>
            </svg>
        </span>
        <?php esc_html_e( 'All', 'toposel-assignment' ); ?>
    </button>

    <button type="button" role="tab" data-filter="men">
        <span class="filter-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
                <circle cx="10" cy="8" r="3"/>
                <path d="M5 19c0-3 2-5 5-5s5 2 5 5"/>
                <path d="M15 5h4v4"/>
                <path d="M19 5l-5 5"/>
            </svg>
        </span>
        <?php esc_html_e( 'Men', 'toposel-assignment' ); ?>
    </button>

    <button type="button" role="tab" data-filter="women">
        <span class="filter-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="7" r="3"/>
                <path d="M7 21l2-7h6l2 7"/>
                <path d="M9 17h6"/>
            </svg>
        </span>
        <?php esc_html_e( 'Women', 'toposel-assignment' ); ?>
    </button>
</div>

					<div class="buymore-explore__tools">
						<button type="button" class="buymore-filters-btn"><?php esc_html_e( 'Filters', 'toposel-assignment' ); ?></button>
						<button type="button" class="buymore-icon-btn buymore-icon-btn--ghost" aria-label="<?php esc_attr_e( 'Search', 'toposel-assignment' ); ?>">
							<svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.4"/><path d="M17 17l-3.5-3.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
						</button>
					</div>
				</div>

				<div class="buymore-grid">

					<!-- hero banner -->
					<?php if ( $banner_large ) : ?>
					<a href="<?php echo esc_url( $banner_large['link'] ? $banner_large['link'] : '#' ); ?>" class="buymore-card buymore-card--hero buymore-card--<?php echo esc_attr( $banner_large['theme'] ); ?>" <?php if ( $banner_large['image'] ) : ?>style="background-image:url(<?php echo esc_url( $banner_large['image'] ); ?>)"<?php endif; ?>>
						<div class="buymore-card__body">
							<h2><?php echo esc_html( $banner_large['title'] ); ?></h2>
							<?php if ( $banner_large['cta'] ) : ?>
								<span class="buymore-card__cta"><?php echo esc_html( $banner_large['cta'] ); ?></span>
							<?php endif; ?>
						</div>
					</a>
					<?php endif; ?>

					<?php if ( $banner_medium ) : ?>
					<a href="<?php echo esc_url( $banner_medium['link'] ? $banner_medium['link'] : '#' ); ?>" class="buymore-card buymore-card--medium buymore-card--<?php echo esc_attr( $banner_medium['theme'] ); ?>" <?php if ( $banner_medium['image'] ) : ?>style="background-image:url(<?php echo esc_url( $banner_medium['image'] ); ?>)"<?php endif; ?>>
						<span class="buymore-card__arrow" aria-hidden="true">&#8599;</span>
						<div class="buymore-card__body">
							<h3><?php echo esc_html( $banner_medium['title'] ); ?></h3>
							<?php if ( $banner_medium['subtitle'] ) : ?>
								<p><?php echo esc_html( $banner_medium['subtitle'] ); ?></p>
							<?php endif; ?>
						</div>
					</a>
					<?php endif; ?>

					<!-- 2 product spotlight cards -->
					<?php foreach ( $spotlights as $i => $product ) : ?>
					<div class="buymore-card buymore-spotlight buymore-spotlight--<?php echo esc_attr( $i + 1 ); ?>" data-cat="<?php echo esc_attr( implode( ' ', $product['cats'] ) ); ?>">
						<div class="buymore-spotlight__image" <?php if ( $product['image'] ) : ?>style="background-image:url(<?php echo esc_url( $product['image'] ); ?>)"<?php endif; ?>>
							<?php if ( ! empty( $product['swatches'] ) ) : ?>
							<span class="buymore-swatches">
								<?php foreach ( $product['swatches'] as $swatch_color ) : ?>
									<span class="buymore-swatches__dot" style="background-color:<?php echo esc_attr( $swatch_color ); ?>"></span>
								<?php endforeach; ?>
							</span>
							<?php endif; ?>
							<button type="button" class="buymore-product__fav" aria-label="<?php esc_attr_e( 'Add to favourites', 'toposel-assignment' ); ?>">&#9825;</button>
						</div>
						<div class="buymore-spotlight__info">
							<?php if ( $product['tag'] ) : ?><span class="buymore-product__tag"><?php echo esc_html( $product['tag'] ); ?></span><?php endif; ?>
							<strong class="buymore-product__title"><?php echo esc_html( $product['title'] ); ?></strong>
							<?php if ( $product['price'] ) : ?><span class="buymore-product__price"><?php echo esc_html( $product['price'] ); ?></span><?php endif; ?>
						</div>
					</div>
					<?php endforeach; ?>

					<!-- small favourites tile + favourites panel -->
					<div class="buymore-card buymore-favtile" <?php if ( $banner_small && $banner_small['image'] ) : ?>style="background-image:url(<?php echo esc_url( $banner_small['image'] ); ?>)"<?php endif; ?>>
						<button type="button" class="buymore-product__fav" aria-label="<?php esc_attr_e( 'Add to favourites', 'toposel-assignment' ); ?>">&#9825;</button>
						<?php if ( $banner_small && $banner_small['cta'] ) : ?>
							<a href="<?php echo esc_url( $banner_small['link'] ? $banner_small['link'] : '#' ); ?>" class="buymore-favtile__cta"><?php echo esc_html( $banner_small['cta'] ); ?></a>
						<?php endif; ?>
					</div>

					<div class="buymore-card buymore-favourites">
						<div class="buymore-favourites__head">
							<span><?php esc_html_e( 'Favourites', 'toposel-assignment' ); ?></span>
							<span class="buymore-favourites__nav">
								<button type="button" aria-label="<?php esc_attr_e( 'Previous', 'toposel-assignment' ); ?>">&lsaquo;</button>
								<button type="button" aria-label="<?php esc_attr_e( 'Next', 'toposel-assignment' ); ?>">&rsaquo;</button>
							</span>
						</div>
						<div class="buymore-favourites__thumbs">
							<?php foreach ( $favs as $i => $fav ) : ?>
								<span class="buymore-favourites__thumb" data-index="<?php echo esc_attr( $i ); ?>" title="<?php echo esc_attr( $fav['title'] ); ?>" <?php echo $i > 1 ? 'hidden' : ''; ?> <?php if ( $fav['image'] ) : ?>style="background-image:url(<?php echo esc_url( $fav['image'] ); ?>)"<?php endif; ?>></span>
							<?php endforeach; ?>
						</div>
						<a href="#" class="buymore-favourites__seeall"><?php esc_html_e( 'See All', 'toposel-assignment' ); ?></a>
					</div>

					<!-- wide bottom banner -->
					<?php if ( $banner_wide ) : ?>
					<a href="<?php echo esc_url( $banner_wide['link'] ? $banner_wide['link'] : '#' ); ?>" class="buymore-card buymore-card--wide buymore-card--<?php echo esc_attr( $banner_wide['theme'] ); ?>" <?php if ( $banner_wide['image'] ) : ?>style="background-image:url(<?php echo esc_url( $banner_wide['image'] ); ?>)"<?php endif; ?>>
						<span class="buymore-card__arrow" aria-hidden="true">&#8599;</span>
						<div class="buymore-card__body">
							<h3><?php echo esc_html( $banner_wide['title'] ); ?></h3>
							<?php if ( $banner_wide['subtitle'] ) : ?>
								<p><?php echo esc_html( $banner_wide['subtitle'] ); ?></p>
							<?php endif; ?>
						</div>
					</a>
					<?php endif; ?>

				</div>
			</section>
		</main>
	</div>
</div>
</div>
