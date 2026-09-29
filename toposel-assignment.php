<?php
/**
 * Plugin Name: Toposel Assignment - BuyMore Dashboard
 * Description:BuyMore e-commerce dashboard UI . Renders via the [toposel_dashboard] 
 * shortcode with fully dynamic, CMS-managed content.
 * Version: 1.0.0
 * Author: Nancy
 * Text Domain: toposel-assignment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TOPOSEL_VERSION', '1.0.0' );
define( 'TOPOSEL_FILE', __FILE__ );
define( 'TOPOSEL_PATH', plugin_dir_path( __FILE__ ) );
define( 'TOPOSEL_URL', plugin_dir_url( __FILE__ ) );


require_once TOPOSEL_PATH . 'includes/backend-post-types.php';
require_once TOPOSEL_PATH . 'includes/backend-meta-boxes.php';
require_once TOPOSEL_PATH . 'includes/backend-settings-page.php';
require_once TOPOSEL_PATH . 'includes/backend-sample-data.php';
require_once TOPOSEL_PATH . 'includes/front-shortcode.php';

register_activation_hook( TOPOSEL_FILE, 'toposel_activate' );

// sidebar menu location - has to run on init (admin needs it too, not just
// the front-end), so it lives here instead of in the template file
add_action( 'init', 'toposel_register_nav_menu_location' );

function toposel_register_nav_menu_location() {
	register_nav_menu( 'toposel_sidebar', __( 'BuyMore Sidebar Navigation', 'toposel-assignment' ) );
}

add_action( 'wp_enqueue_scripts', 'toposel_enqueue_assets' );

function toposel_enqueue_assets() {
	if ( ! toposel_page_has_shortcode() ) {
		return;
	}

	// heading/number font used in the reference design
	wp_enqueue_style( 'toposel-google-font', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap', array(), null );

	wp_enqueue_style( 'toposel-dashboard', TOPOSEL_URL . 'assets/css/style.css', array( 'toposel-google-font' ), TOPOSEL_VERSION );

	wp_enqueue_script( 'toposel-dashboard', TOPOSEL_URL . 'assets/js/script.js', array( 'jquery' ), TOPOSEL_VERSION, true );
}

// runs once when the plugin is activated
function toposel_activate() {

	toposel_register_post_types();
	toposel_register_taxonomies();	
	toposel_seed_sample_data();


}

