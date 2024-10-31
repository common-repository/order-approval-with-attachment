<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://sevengits.com/
 * @since      1.0.0
 *
 * @package    Sg_Woocommerce_Order_Approval_With_Attachment
 * @subpackage Sg_Woocommerce_Order_Approval_With_Attachment/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sg_Woocommerce_Order_Approval_With_Attachment
 * @subpackage Sg_Woocommerce_Order_Approval_With_Attachment/includes
 * @author     Sevengits <support@sevengits.com>
 */
class Sg_Woocommerce_Order_Approval_With_Attachment_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sg-woocommerce-order-approval-with-attachment',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
