<?php
// settings page for the header stats (orders count, cart count etc)
// these are not tied to a single post so they live in wp_options

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TOPOSEL_SETTINGS_KEY', 'toposel_dashboard_settings' );

add_action( 'admin_menu', 'toposel_register_settings_menu' );
add_action( 'admin_init', 'toposel_register_settings_fields' );

// default values, used if nothing is saved yet
function toposel_settings_defaults() {
	return array(
		'store_name'     => 'BuyMore',
		'orders_count'   => '37',
		'orders_period'  => 'Last 7 days',
		'cart_count'     => '2',
		'guest_name'     => 'Ryana',
		'explore_intro'  => 'Explore',
		'shortcode_hint' => '[toposel_dashboard]',
	);
}

// get one setting value, fall back to default if not saved
function toposel_get_setting( $key ) {
	$opts = wp_parse_args( get_option( TOPOSEL_SETTINGS_KEY, array() ), toposel_settings_defaults() );
	return isset( $opts[ $key ] ) ? $opts[ $key ] : '';
}

// admin menu: BuyMore Dashboard (also parent menu for the CPTs)
function toposel_register_settings_menu() {
	add_menu_page(
		__( 'BuyMore Dashboard', 'toposel-assignment' ),
		__( 'BuyMore Dashboard', 'toposel-assignment' ),
		'manage_options',
		'toposel-dashboard',
		'toposel_render_settings_page',
		'',
		25
	);

	add_submenu_page(
		'toposel-dashboard',
		__( 'General Settings', 'toposel-assignment' ),
		__( 'Settings', 'toposel-assignment' ),
		'manage_options',
		'toposel-dashboard',
		'toposel_render_settings_page'
	);
}

function toposel_register_settings_fields() {
	register_setting( 'toposel_settings_group', TOPOSEL_SETTINGS_KEY, 'toposel_sanitize_settings' );

	add_settings_section( 'toposel_main', __( 'Dashboard Header & Stats', 'toposel-assignment' ), '__return_false', 'toposel-dashboard' );

	$fields = array(
		'store_name'    => __( 'Store name (top-left logo text)', 'toposel-assignment' ),
		'orders_count'  => __( 'Orders count', 'toposel-assignment' ),
		'orders_period' => __( 'Orders period label', 'toposel-assignment' ),
		'cart_count'    => __( 'Cart item count', 'toposel-assignment' ),
		'guest_name'    => __( 'Fallback display name (used if no user is logged in)', 'toposel-assignment' ),
		'explore_intro' => __( 'Explore section heading', 'toposel-assignment' ),
	);

	foreach ( $fields as $key => $label ) {
		add_settings_field(
			$key,
			$label,
			'toposel_render_settings_field',
			'toposel-dashboard',
			'toposel_main',
			array( 'key' => $key )
		);
	}
}

function toposel_render_settings_field( $args ) {
	$key   = $args['key'];
	$value = toposel_get_setting( $key );
	printf(
		'<input type="text" class="regular-text" name="%1$s[%2$s]" value="%3$s">',
		esc_attr( TOPOSEL_SETTINGS_KEY ),
		esc_attr( $key ),
		esc_attr( $value )
	);
}

function toposel_sanitize_settings( $input ) {
	$clean = array();
	foreach ( toposel_settings_defaults() as $key => $default ) {
		$clean[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( wp_unslash( $input[ $key ] ) ) : $default;
	}
	return $clean;
}

function toposel_render_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'BuyMore Dashboard Settings', 'toposel-assignment' ); ?></h1>
		<p>
			<?php esc_html_e( 'Place the dashboard on any page with the shortcode:', 'toposel-assignment' ); ?>
			<code><?php echo esc_html( toposel_get_setting( 'shortcode_hint' ) ); ?></code>
		</p>
		
		<form method="post" action="options.php">
			<?php
			settings_fields( 'toposel_settings_group' );
			do_settings_sections( 'toposel-dashboard' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
