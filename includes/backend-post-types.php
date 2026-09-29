<?php
// custom post types + taxonomy used by the dashboard

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'toposel_register_post_types' );
add_action( 'init', 'toposel_register_taxonomies' );

function toposel_register_post_types() {

	// post type: Products (shown in the "Explore" grid)
	register_post_type( 'toposel_product', array(
		'labels' => array(
			'name'          => __( 'Products', 'toposel-assignment' ),
			'singular_name' => __( 'Product', 'toposel-assignment' ),
			'add_new_item'  => __( 'Add New Product', 'toposel-assignment' ),
			'edit_item'     => __( 'Edit Product', 'toposel-assignment' ),
			'all_items'     => __( 'Products', 'toposel-assignment' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'toposel-dashboard',
		'menu_icon'    => 'dashicons-tag',
		'supports'     => array( 'title', 'thumbnail' ),
		'has_archive'  => false,
		'rewrite'      => false,
	) );

	// post type: Promo Banners (the hero / winter's weekend / wide cards)
	register_post_type( 'toposel_banner', array(
		'labels' => array(
			'name'          => __( 'Promo Banners', 'toposel-assignment' ),
			'singular_name' => __( 'Promo Banner', 'toposel-assignment' ),
			'add_new_item'  => __( 'Add New Banner', 'toposel-assignment' ),
			'edit_item'     => __( 'Edit Banner', 'toposel-assignment' ),
			'all_items'     => __( 'Promo Banners', 'toposel-assignment' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'toposel-dashboard',
		'menu_icon'    => 'dashicons-images-alt2',
		'supports'     => array( 'title', 'thumbnail' ),
		'has_archive'  => false,
		'rewrite'      => false,
	) );

	// post type: Recent Orders (sidebar "Last orders" list)
	register_post_type( 'toposel_order', array(
		'labels' => array(
			'name'          => __( 'Recent Orders', 'toposel-assignment' ),
			'singular_name' => __( 'Order', 'toposel-assignment' ),
			'add_new_item'  => __( 'Add New Order', 'toposel-assignment' ),
			'edit_item'     => __( 'Edit Order', 'toposel-assignment' ),
			'all_items'     => __( 'Recent Orders', 'toposel-assignment' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'toposel-dashboard',
		'menu_icon'    => 'dashicons-cart',
		'supports'     => array( 'title', 'thumbnail' ),
		'has_archive'  => false,
		'rewrite'      => false,
	) );
}

// taxonomy: Product Categories (Men / Women) - used by the filter tabs
function toposel_register_taxonomies() {
	register_taxonomy( 'toposel_product_cat', 'toposel_product', array(
		'labels' => array(
			'name'          => __( 'Product Categories', 'toposel-assignment' ),
			'singular_name' => __( 'Category', 'toposel-assignment' ),
		),
		'public'            => true,
		'show_ui'           => true,
		'show_in_menu'      => 'toposel-dashboard',
		'hierarchical'      => true,
		'show_admin_column' => true,
	) );
}
