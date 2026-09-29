<?php
// [toposel_dashboard] shortcode - pulls the data and renders the template


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'toposel_dashboard', 'toposel_dashboard_shortcode' );

// used by assets.php to only load css/js on pages that use the shortcode
function toposel_page_has_shortcode() {
	if ( is_admin() ) {
		return false;
	}
	global $post;
	return $post instanceof WP_Post && has_shortcode( $post->post_content, 'toposel_dashboard' );
}

function toposel_dashboard_shortcode() {
	// collect everything the template needs in one array
	$data = array(
		'settings' => toposel_get_dashboard_settings(),
		'user'     => toposel_get_dashboard_user(),
		'banners'  => toposel_get_banners(),
		'popular'  => toposel_get_products( array( 'meta_key' => '_toposel_popular', 'meta_value' => '1', 'posts_per_page' => 5 ) ),
		'explore'  => toposel_get_products( array( 'posts_per_page' => 4 ) ),
		'favs'     => toposel_get_products( array( 'meta_key' => '_toposel_favourite', 'meta_value' => '1', 'posts_per_page' => 4 ) ),
		'orders'   => toposel_get_orders(),
		'cats'     => get_terms( array( 'taxonomy' => 'toposel_product_cat', 'hide_empty' => false ) ),
	);

	ob_start();
	toposel_load_template( 'front-dashboard-template.php', $data );
	return ob_get_clean();
}

function toposel_load_template( $file, $data ) {
	$path = TOPOSEL_PATH . 'templates/' . $file;
	if ( file_exists( $path ) ) {
		extract( $data ); // phpcs:ignore -- controlled, fixed key template data.
		include $path;
	}
}

function toposel_get_dashboard_settings() {
	$keys = array( 'store_name', 'orders_count', 'orders_period', 'cart_count', 'guest_name', 'explore_intro' );
	$out  = array();
	foreach ( $keys as $key ) {
		$out[ $key ] = toposel_get_setting( $key );
	}
	return $out;
}

function toposel_get_dashboard_user() {
	if ( is_user_logged_in() ) {
		$current = wp_get_current_user();
		return array(
			'name'   => $current->display_name ? $current->display_name : $current->user_login,
			'avatar' => get_avatar_url( $current->ID, array( 'size' => 80 ) ),
		);
	}
	return array(
		'name'   => toposel_get_setting( 'guest_name' ),
		'avatar' => '',
	);
}

// promo banners (hero / winter's weekend / small / wide cards)
function toposel_get_banners() {
	$posts = get_posts( array(
		'post_type'      => 'toposel_banner',
		'posts_per_page' => 6,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	) );

	$banners = array();
	foreach ( $posts as $post ) {
		$banners[] = array(
			'id'       => $post->ID,
			'title'    => get_the_title( $post ),
			'subtitle' => get_post_meta( $post->ID, '_toposel_subtitle', true ),
			'cta'      => get_post_meta( $post->ID, '_toposel_cta', true ),
			'link'     => get_post_meta( $post->ID, '_toposel_link', true ),
			'size'     => get_post_meta( $post->ID, '_toposel_size', true ) ?: 'medium',
			'theme'    => get_post_meta( $post->ID, '_toposel_theme', true ) ?: 'light',
			'image'    => get_the_post_thumbnail_url( $post->ID, 'large' ),
		);
	}
	return $banners;
}

// products for the explore grid / favourites / popular list
function toposel_get_products( $extra_args = array() ) {
	$args = wp_parse_args( $extra_args, array(
		'post_type'      => 'toposel_product',
		'posts_per_page' => 4,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	) );

	$posts    = get_posts( $args );
	$products = array();
	foreach ( $posts as $post ) {
		$cats     = wp_get_post_terms( $post->ID, 'toposel_product_cat', array( 'fields' => 'slugs' ) );
		$swatches = get_post_meta( $post->ID, '_toposel_swatches', true );

		$products[] = array(
			'id'       => $post->ID,
			'title'    => get_the_title( $post ),
			'tag'      => get_post_meta( $post->ID, '_toposel_tag', true ),
			'price'    => get_post_meta( $post->ID, '_toposel_price', true ),
			'image'    => get_the_post_thumbnail_url( $post->ID, 'medium' ),
			'cats'     => $cats,
			'swatches' => $swatches ? array_map( 'trim', explode( ',', $swatches ) ) : array(),
		);
	}
	return $products;
}

// sidebar "last orders" list
function toposel_get_orders() {
	$posts  = get_posts( array(
		'post_type'      => 'toposel_order',
		'posts_per_page' => 2,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	$orders = array();
	foreach ( $posts as $post ) {
		$orders[] = array(
			'id'     => $post->ID,
			'title'  => get_the_title( $post ),
			'status' => get_post_meta( $post->ID, '_toposel_status', true ) ?: __( 'view order', 'toposel-assignment' ),
			'image'  => get_the_post_thumbnail_url( $post->ID, 'thumbnail' ),
		);
	}
	return $orders;
}
