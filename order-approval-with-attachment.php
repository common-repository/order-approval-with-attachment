<?php
/**
 * Plugin Name:       Order Approval with attachment
 * Plugin URI:        https://sevengits.com/plugin/order-approval-with-attachment/
 * Description:       This plugin will help customers to upload an attachment during checkout. Later shop owners will approve/ reject the order based on attachment.
 * Version:           1.0.0
 * Author:            Sevengits
 * Author URI:        https://sevengits.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       order-approval-with-attachment
 * Domain Path:       /languages
 * WC requires at least: 3.7
 * WC tested up to: 5.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if(! defined( 'SGOAA_VER' ) ){
	define( 'SGOAA_VER', '1.0.0' );
}
if ( ! defined( 'SG_BASE_ORDER_ATTACHMENT' ) ) {
	define( 'SGOAA_BASE_ORDER_ATTACHMENT', plugin_basename( __FILE__ ) );
}
if(! defined( 'SGOAA_PLUGIN_PATH_ORDER_ATTACHMENT' ) ){
	define( 'SGOAA_PLUGIN_PATH_ORDER_ATTACHMENT', plugin_dir_path( __FILE__ ) );
}




/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sg-woocommerce-order-approval-with-attachment-activator.php
 */
function sgoaa_activate_woocommerce_order_approval_with_attachment() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sg-woocommerce-order-approval-with-attachment-activator.php';
	Sg_Woocommerce_Order_Approval_With_Attachment_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sg-woocommerce-order-approval-with-attachment-deactivator.php
 */
function sgoaa_deactivate_woocommerce_order_approval_with_attachment() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sg-woocommerce-order-approval-with-attachment-deactivator.php';
	Sg_Woocommerce_Order_Approval_With_Attachment_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'sgoaa_activate_woocommerce_order_approval_with_attachment' );
register_deactivation_hook( __FILE__, 'sgoaa_deactivate_woocommerce_order_approval_with_attachment' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sg-woocommerce-order-approval-with-attachment.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sgoaa_woocommerce_order_approval_with_attachment() {

	$plugin = new Sg_Woocommerce_Order_Approval_With_Attachment();
	$plugin->run();

}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    run_sgoaa_woocommerce_order_approval_with_attachment();
}