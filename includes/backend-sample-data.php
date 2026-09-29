<?php
// adds demo content on activation so the dashboard is not empty
// runs only once, checked with the option flag below

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TOPOSEL_SEEDED_FLAG', 'toposel_sample_data_seeded' );

function toposel_seed_sample_data() {
	if ( get_option( TOPOSEL_SEEDED_FLAG ) ) {
		return;
	}

	// post types/taxonomy need to exist before we can insert into them
	toposel_register_post_types();
	toposel_register_taxonomies();

	// category terms: Men / Women
	$men      = wp_insert_term( 'Men', 'toposel_product_cat' );
	$women    = wp_insert_term( 'Women', 'toposel_product_cat' );
	$men_id   = is_wp_error( $men ) ? 0 : $men['term_id'];
	$women_id = is_wp_error( $women ) ? 0 : $women['term_id'];

	toposel_seed_products( $men_id, $women_id );
	toposel_seed_banners();
	toposel_seed_orders();
	toposel_seed_menu();

	update_option( TOPOSEL_SEEDED_FLAG, 1 );
}

function toposel_seed_products( $men_id, $women_id ) {
	$products = array(
		array( 'title' => 'WMX Rubber Zebra Sandal', 'tag' => 'Our Picks', 'price' => '$36', 'cat' => $men_id, 'popular' => true, 'swatches' => '#f7b6c2,#f2d24b' ),
		array( 'title' => 'Supper Skiny Jogger in Brown', 'tag' => 'Your Choice', 'price' => '$89', 'cat' => $men_id, 'popular' => true, 'swatches' => '#f2d24b,#1c2b24' ),
		array( 'title' => 'Layered Puffer Jacket', 'tag' => 'Trending', 'price' => '$120', 'cat' => $women_id, 'favourite' => true ),
		array( 'title' => 'Classic Canvas Sneaker', 'tag' => 'New In', 'price' => '$54', 'cat' => $men_id, 'favourite' => true ),
		array( 'title' => 'Oversized Knit Sweater', 'tag' => 'Best Seller', 'price' => '$62', 'cat' => $women_id, 'popular' => true, 'favourite' => true ),
	);

	foreach ( $products as $p ) {
		$id = wp_insert_post( array(
			'post_type'   => 'toposel_product',
			'post_title'  => $p['title'],
			'post_status' => 'publish',
		) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_toposel_tag', $p['tag'] );
		update_post_meta( $id, '_toposel_price', $p['price'] );
		if ( ! empty( $p['swatches'] ) ) {
			update_post_meta( $id, '_toposel_swatches', $p['swatches'] );
		}
		if ( ! empty( $p['popular'] ) ) {
			update_post_meta( $id, '_toposel_popular', '1' );
		}
		if ( ! empty( $p['favourite'] ) ) {
			update_post_meta( $id, '_toposel_favourite', '1' );
		}
		if ( ! empty( $p['cat'] ) ) {
			wp_set_post_terms( $id, array( (int) $p['cat'] ), 'toposel_product_cat' );
		}
	}
}

function toposel_seed_banners() {
	$banners = array(
		array( 'title' => 'Get Up To 50% Off', 'subtitle' => '', 'cta' => 'Get Discount', 'size' => 'large', 'theme' => 'light' ),
		array( 'title' => "Winter's Weekend", 'subtitle' => 'keep it casual', 'cta' => '', 'size' => 'medium', 'theme' => 'dark' ),
		array( 'title' => 'Favourites Pick', 'subtitle' => '', 'cta' => 'Avail Offers', 'size' => 'small', 'theme' => 'dark' ),
		array( 'title' => 'Bring Bold Fashion', 'subtitle' => 'Layers on Layers', 'cta' => '', 'size' => 'wide', 'theme' => 'dark' ),
	);

	foreach ( $banners as $b ) {
		$id = wp_insert_post( array(
			'post_type'   => 'toposel_banner',
			'post_title'  => $b['title'],
			'post_status' => 'publish',
		) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_toposel_subtitle', $b['subtitle'] );
		update_post_meta( $id, '_toposel_cta', $b['cta'] );
		update_post_meta( $id, '_toposel_size', $b['size'] );
		update_post_meta( $id, '_toposel_theme', $b['theme'] );
	}
}

function toposel_seed_orders() {
	$orders = array(
		array( 'title' => 'DXC Nike...', 'status' => 'view order' ),
		array( 'title' => 'Outerwear...', 'status' => 'view order' ),
	);

	foreach ( $orders as $o ) {
		$id = wp_insert_post( array(
			'post_type'   => 'toposel_order',
			'post_title'  => $o['title'],
			'post_status' => 'publish',
		) );
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_toposel_status', $o['status'] );
	}
}

function toposel_seed_menu() {
	if ( ! function_exists( 'wp_create_nav_menu' ) ) {
		return;
	}

	$menu_name = 'BuyMore Sidebar';
	$menu_id   = wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		$existing = get_term_by( 'name', $menu_name, 'nav_menu' );
		$menu_id  = $existing ? $existing->term_id : 0;
	}
	if ( ! $menu_id ) {
		return;
	}

	$items = array(
		array( 'title' => 'Popular Products', 'class' => 'icon-popular' ),
		array( 'title' => 'Explore New', 'class' => 'icon-explore is-default-active' ),
		array( 'title' => 'Clothing and Shoes', 'class' => 'icon-clothing' ),
		array( 'title' => 'Gifts and Living', 'class' => 'icon-gifts' ),
		array( 'title' => 'Inspiration', 'class' => 'icon-inspiration' ),
	);

	foreach ( $items as $item ) {
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'   => $item['title'],
			'menu-item-classes' => $item['class'],
			'menu-item-url'     => '#',
			'menu-item-status'  => 'publish',
		) );
	}

	$locations                    = get_theme_mod( 'nav_menu_locations' );
	$locations                    = is_array( $locations ) ? $locations : array();
	$locations['toposel_sidebar'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
