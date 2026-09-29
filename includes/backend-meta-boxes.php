<?php
// meta boxes for product / banner / order post types
// this is what lets an editor change price, tags, banner text etc from wp-admin

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', 'toposel_register_meta_boxes' );
add_action( 'save_post_toposel_product', 'toposel_save_product_meta' );
add_action( 'save_post_toposel_banner', 'toposel_save_banner_meta' );
add_action( 'save_post_toposel_order', 'toposel_save_order_meta' );

function toposel_register_meta_boxes() {
	add_meta_box( 'toposel_product_details', __( 'Product Details', 'toposel-assignment' ), 'toposel_render_product_meta_box', 'toposel_product', 'normal', 'high' );
	add_meta_box( 'toposel_banner_details', __( 'Banner Details', 'toposel-assignment' ), 'toposel_render_banner_meta_box', 'toposel_banner', 'normal', 'high' );
	add_meta_box( 'toposel_order_details', __( 'Order Details', 'toposel-assignment' ), 'toposel_render_order_meta_box', 'toposel_order', 'normal', 'high' );
}

/* ---------------- Product ---------------- */

function toposel_render_product_meta_box( $post ) {
	wp_nonce_field( 'toposel_save_product', 'toposel_product_nonce' );
	$price     = get_post_meta( $post->ID, '_toposel_price', true );
	$tag       = get_post_meta( $post->ID, '_toposel_tag', true );
	$favourite = get_post_meta( $post->ID, '_toposel_favourite', true );
	$popular   = get_post_meta( $post->ID, '_toposel_popular', true );
	$swatches  = get_post_meta( $post->ID, '_toposel_swatches', true );
	?>
	<p>
		<label for="toposel_price"><strong><?php esc_html_e( 'Price for eg - $36', 'toposel-assignment' ); ?></strong></label><br>
		<input type="text" id="toposel_price" name="toposel_price" class="widefat" value="<?php echo esc_attr( $price ); ?>">
	</p>
	<p>
		<label for="toposel_tag"><strong><?php esc_html_e( 'Small tag above title (e.g. "Our Picks")', 'toposel-assignment' ); ?></strong></label><br>
		<input type="text" id="toposel_tag" name="toposel_tag" class="widefat" value="<?php echo esc_attr( $tag ); ?>">
	</p>
	<p>
		<label for="toposel_swatches"><strong><?php esc_html_e( 'Color swatch dots, comma separated hex (e.g. #f7b6c2,#f5deb3)', 'toposel-assignment' ); ?></strong></label><br>
		<input type="text" id="toposel_swatches" name="toposel_swatches" class="widefat" value="<?php echo esc_attr( $swatches ); ?>">
	</p>
	<p>
		<label>
			<input type="checkbox" name="toposel_favourite" value="1" <?php checked( $favourite, '1' ); ?>>
			<?php esc_html_e( 'Show in "Favourites" carousel', 'toposel-assignment' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="toposel_popular" value="1" <?php checked( $popular, '1' ); ?>>
			<?php esc_html_e( 'Show in "Popular Products" list', 'toposel-assignment' ); ?>
		</label>
	</p>
	<?php
}

function toposel_save_product_meta( $post_id ) {
	// nonce + permission check before writing anything
	if ( ! isset( $_POST['toposel_product_nonce'] ) || ! wp_verify_nonce( $_POST['toposel_product_nonce'], 'toposel_save_product' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_toposel_price', isset( $_POST['toposel_price'] ) ? sanitize_text_field( wp_unslash( $_POST['toposel_price'] ) ) : '' );
	update_post_meta( $post_id, '_toposel_tag', isset( $_POST['toposel_tag'] ) ? sanitize_text_field( wp_unslash( $_POST['toposel_tag'] ) ) : '' );
	update_post_meta( $post_id, '_toposel_swatches', isset( $_POST['toposel_swatches'] ) ? sanitize_text_field( wp_unslash( $_POST['toposel_swatches'] ) ) : '' );
	update_post_meta( $post_id, '_toposel_favourite', isset( $_POST['toposel_favourite'] ) ? '1' : '' );
	update_post_meta( $post_id, '_toposel_popular', isset( $_POST['toposel_popular'] ) ? '1' : '' );
}

/* ---------------- Banner ---------------- */

function toposel_render_banner_meta_box( $post ) {
	wp_nonce_field( 'toposel_save_banner', 'toposel_banner_nonce' );
	$subtitle = get_post_meta( $post->ID, '_toposel_subtitle', true );
	$cta      = get_post_meta( $post->ID, '_toposel_cta', true );
	$link     = get_post_meta( $post->ID, '_toposel_link', true );
	$size     = get_post_meta( $post->ID, '_toposel_size', true );
	$theme    = get_post_meta( $post->ID, '_toposel_theme', true );
	if ( ! $size ) {
		$size = 'medium';
	}
	if ( ! $theme ) {
		$theme = 'light';
	}
	?>
	<p>
		<label for="toposel_subtitle"><strong><?php esc_html_e( 'Subtitle', 'toposel-assignment' ); ?></strong></label><br>
		<input type="text" id="toposel_subtitle" name="toposel_subtitle" class="widefat" value="<?php echo esc_attr( $subtitle ); ?>">
	</p>
	<p>
		<label for="toposel_cta"><strong><?php esc_html_e( 'Button / CTA text (leave blank to hide)', 'toposel-assignment' ); ?></strong></label><br>
		<input type="text" id="toposel_cta" name="toposel_cta" class="widefat" value="<?php echo esc_attr( $cta ); ?>">
	</p>
	<p>
		<label for="toposel_link"><strong><?php esc_html_e( 'Link URL', 'toposel-assignment' ); ?></strong></label><br>
		<input type="url" id="toposel_link" name="toposel_link" class="widefat" value="<?php echo esc_attr( $link ); ?>">
	</p>
	<p>
		<label for="toposel_size"><strong><?php esc_html_e( 'Grid size', 'toposel-assignment' ); ?></strong></label><br>
		<select id="toposel_size" name="toposel_size">
			<option value="large" <?php selected( $size, 'large' ); ?>><?php esc_html_e( 'Hero (top banner)', 'toposel-assignment' ); ?></option>
			<option value="medium" <?php selected( $size, 'medium' ); ?>><?php esc_html_e( 'Medium (below hero)', 'toposel-assignment' ); ?></option>
			<option value="small" <?php selected( $size, 'small' ); ?>><?php esc_html_e( 'Small (favourites tile)', 'toposel-assignment' ); ?></option>
			<option value="wide" <?php selected( $size, 'wide' ); ?>><?php esc_html_e( 'Wide (bottom banner)', 'toposel-assignment' ); ?></option>
		</select>
	</p>
	<p>
		<label for="toposel_theme"><strong><?php esc_html_e( 'Text theme', 'toposel-assignment' ); ?></strong></label><br>
		<select id="toposel_theme" name="toposel_theme">
			<option value="light" <?php selected( $theme, 'light' ); ?>><?php esc_html_e( 'Light text (for dark images)', 'toposel-assignment' ); ?></option>
			<option value="dark" <?php selected( $theme, 'dark' ); ?>><?php esc_html_e( 'Dark text (for light images)', 'toposel-assignment' ); ?></option>
		</select>
	</p>
	<?php
}

function toposel_save_banner_meta( $post_id ) {
	if ( ! isset( $_POST['toposel_banner_nonce'] ) || ! wp_verify_nonce( $_POST['toposel_banner_nonce'], 'toposel_save_banner' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_toposel_subtitle', isset( $_POST['toposel_subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['toposel_subtitle'] ) ) : '' );
	update_post_meta( $post_id, '_toposel_cta', isset( $_POST['toposel_cta'] ) ? sanitize_text_field( wp_unslash( $_POST['toposel_cta'] ) ) : '' );
	update_post_meta( $post_id, '_toposel_link', isset( $_POST['toposel_link'] ) ? esc_url_raw( wp_unslash( $_POST['toposel_link'] ) ) : '' );
	update_post_meta( $post_id, '_toposel_size', isset( $_POST['toposel_size'] ) ? sanitize_key( $_POST['toposel_size'] ) : 'medium' );
	update_post_meta( $post_id, '_toposel_theme', isset( $_POST['toposel_theme'] ) ? sanitize_key( $_POST['toposel_theme'] ) : 'light' );
}

/* ---------------- Order ---------------- */

function toposel_render_order_meta_box( $post ) {
	wp_nonce_field( 'toposel_save_order', 'toposel_order_nonce' );
	$status = get_post_meta( $post->ID, '_toposel_status', true );
	?>
	<p>
		<label for="toposel_status"><strong><?php esc_html_e( 'Status label (e.g. "view order")', 'toposel-assignment' ); ?></strong></label><br>
		<input type="text" id="toposel_status" name="toposel_status" class="widefat" value="<?php echo esc_attr( $status ); ?>">
	</p>
	<p class="description"><?php esc_html_e( 'Set a featured image to use as the small order thumbnail. Post title is the order label (e.g. "DXC Nike...").', 'toposel-assignment' ); ?></p>
	<?php
}

function toposel_save_order_meta( $post_id ) {
	if ( ! isset( $_POST['toposel_order_nonce'] ) || ! wp_verify_nonce( $_POST['toposel_order_nonce'], 'toposel_save_order' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_toposel_status', isset( $_POST['toposel_status'] ) ? sanitize_text_field( wp_unslash( $_POST['toposel_status'] ) ) : '' );
}
