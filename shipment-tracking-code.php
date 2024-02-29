<?php
/*
Plugin Name: Shipment tracking tab
Description: Add tracking code tab
Version: 1.0.0
Author: Vladimir
*/

// Add custom shipment tracking field to order page
add_action( 'add_meta_boxes', 'add_shipment_tracking_meta_box' );
function add_shipment_tracking_meta_box() {
    add_meta_box(
        'shipment_tracking_meta_box',
        __( 'Shipment Tracking', 'woocommerce' ),
        'render_shipment_tracking_meta_box',
        'shop_order',
        'side',
        'default'
    );
}

// Render the shipment tracking meta box content
function render_shipment_tracking_meta_box( $post ) {
    $tracking_code = get_post_meta( $post->ID, '_shipment_tracking', true );
    ?>
    <p>
        <label for="_shipment_tracking"><?php esc_html_e( 'Tracking Code', 'woocommerce' ); ?></label>
        <br />
        <input type="text" name="_shipment_tracking" id="_shipment_tracking" value="<?php echo esc_attr( $tracking_code ); ?>" class="regular-text" />
    </p>
    <?php
}

// Save custom shipment tracking field
add_action( 'woocommerce_process_shop_order_meta', 'save_shipment_tracking_field', 10, 2 );
function save_shipment_tracking_field( $order_id, $post_data ) {
    if ( isset( $_POST['_shipment_tracking'] ) ) {
        $tracking_code = sanitize_text_field( $_POST['_shipment_tracking'] );
        update_post_meta( $order_id, '_shipment_tracking', $tracking_code );
    }
}

// Add custom column to the All Orders page
add_filter( 'manage_edit-shop_order_columns', 'add_tracking_column' );
function add_tracking_column( $columns ) {
    $columns['tracking_code'] = __( 'Tracking Code', 'woocommerce' );
    return $columns;
}

// Populate the custom column with tracking codes and track buttons
add_action( 'manage_shop_order_posts_custom_column', 'populate_custom_tracking_column', 10, 2 );
function populate_custom_tracking_column( $column, $post_id ) {
    if ( $column === 'tracking_code' ) {
        $tracking_code = get_post_meta( $post_id, '_shipment_tracking', true );
        if ( $tracking_code ) {
            echo '<div class="tracking-code-wrapper">';
            echo '<span class="tracking-code">' . esc_html( $tracking_code ) . '</span><br/>';
			echo 'Track with:<br />';
            echo '<a href="#" class="track-button omniva" data-tracking-code="' . esc_attr( $tracking_code ) . '">OMNIVA</a><br/>';
            echo '<a href="#" class="track-button post" data-tracking-code="' . esc_attr( $tracking_code ) . '">LPexpress</a><br/>';
            echo '<a href="#" class="track-button venipak" data-tracking-code="' . esc_attr( $tracking_code ) . '">VENIPAK</a><br/>';
            echo '</div>';
        } else {
            echo '-';
        }
    }
}

// Enqueue JavaScript to handle track button functionality
add_action( 'admin_enqueue_scripts', 'enqueue_track_button_script' );
function enqueue_track_button_script() {
    wp_enqueue_script( 'track-button', get_stylesheet_directory_uri() . '/track-button.js', array( 'jquery' ), '1.0.0', true );
}
