<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://sevengits.com/
 * @since      1.0.0
 *
 * @package    Sg_Woocommerce_Order_Approval_With_Attachment
 * @subpackage Sg_Woocommerce_Order_Approval_With_Attachment/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sg_Woocommerce_Order_Approval_With_Attachment
 * @subpackage Sg_Woocommerce_Order_Approval_With_Attachment/includes
 * @author     Sevengits <support@sevengits.com>
 */
class Sg_Woocommerce_Order_Approval_With_Attachment {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sg_Woocommerce_Order_Approval_With_Attachment_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SGOAA_VER' ) ) {
			$this->version = SGOAA_VER;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sg-woocommerce-order-approval-with-attachment';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sg_Woocommerce_Order_Approval_With_Attachment_Loader. Orchestrates the hooks of the plugin.
	 * - Sg_Woocommerce_Order_Approval_With_Attachment_i18n. Defines internationalization functionality.
	 * - Sg_Woocommerce_Order_Approval_With_Attachment_Admin. Defines all hooks for the admin area.
	 * - Sg_Woocommerce_Order_Approval_With_Attachment_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
/**
		 * The class responsible for defining email template
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sg-order-approval-woocommerce-pro-wc_email.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sg-woocommerce-order-approval-with-attachment-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sg-woocommerce-order-approval-with-attachment-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sg-woocommerce-order-approval-with-attachment-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sg-woocommerce-order-approval-with-attachment-public.php';

		$this->loader = new Sg_Woocommerce_Order_Approval_With_Attachment_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sg_Woocommerce_Order_Approval_With_Attachment_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sg_Woocommerce_Order_Approval_With_Attachment_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Sg_Woocommerce_Order_Approval_With_Attachment_Admin( $this->get_plugin_name(), $this->get_version() );
		if(isset($_GET['section']) && $_GET['section'] == 'sg_order_attach_tab'){
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		}
		$this->loader->add_action( 'init', $plugin_admin,'oawoo_register_my_new_order_statuse_wc_waiting' );
		$this->loader->add_filter( 'wc_order_statuses',$plugin_admin, 'oawoo_wc_order_statuse_wc_waiting' );
		$this->loader->add_filter( 'woocommerce_admin_order_actions',$plugin_admin, 'oawoo_add_custom_order_status_actions_button',100, 2 );
		//add css class and icons for approve nd reject button
		$this->loader->add_action( 'admin_head', $plugin_admin,'oawoo_add_custom_order_status_actions_button_css' );
		$this->loader->add_filter( 'woocommerce_payment_gateways',$plugin_admin, 'oawoo_wc_add_to_gateways' );
		// add plugin setting link
		$this->loader->add_filter( 'plugin_action_links_' .SGOAA_BASE_ORDER_ATTACHMENT, $plugin_admin, 'oawoo_wc_gateway_plugin_links' );
		// woo payment gateway
		$this->loader->add_action( 'plugins_loaded', $plugin_admin,'oawoo_wc_gateway_init', 11 );
		$this->loader->add_action('woocommerce_order_status_waiting',$plugin_admin, 'email_waiting_new_order_notifications', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_status_waiting_to_pending',$plugin_admin, 'oawoo_status_waiting_approved_notification', 100, 2 );
		$this->loader->add_action( 'woocommerce_order_status_waiting_to_cancelled', $plugin_admin,'oawoo_status_waiting_rejected_notification', 100, 2 );
		
		// add setting page
		$this->loader->add_filter( 'woocommerce_get_sections_advanced', $plugin_admin, 'sg_add_settings_tab' );
		$this->loader->add_filter( 'woocommerce_get_settings_advanced' , $plugin_admin, 'sg_get_settings' , 10, 2 );
			
		// Show checkbox is per product order approval is enabled
		if(get_option( 'sg_enable_order_approval' )=='disable'){ 

			$this->loader->add_action( 'woocommerce_product_options_advanced', $plugin_admin,  'sg_preorder_check_product_options');
			$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin,'sg_save_fields', 10, 2 );
		}	
		// make waiting status orders are editable
		if(get_option( 'sg_enable_order_edit' )=='yes'){
			$this->loader->add_filter( 'wc_order_is_editable', $plugin_admin, 'wc_make_processing_orders_editable', 10, 2 );
		}
		$this->loader->add_action('wp_ajax_sg_attachment_upload', $plugin_admin,'sg_imageupload_checkout_field_process');
		$this->loader->add_action('wp_ajax_nopriv_sg_attachment_upload', $plugin_admin,'sg_imageupload_checkout_field_process');
		// change folder name to order id
		$this->loader->add_action('woocommerce_checkout_order_processed',  $plugin_admin,'sg_change_folder_name', 100, 1);
		// add meta box in order edit page
		$this->loader->add_action( 'add_meta_boxes',$plugin_admin, 'sg_attachment_add_meta_boxes' );
		// delete attachment folder when order trashed
		$this->loader->add_action( 'wp_trash_post',$plugin_admin, 'sg_attachment_action_woocommerce_trash_order', 10, 1 ); 
		// validate checkout page for attachments.
		$this->loader->add_action( 'woocommerce_after_checkout_validation',$plugin_admin, 'sg_checkout_attachment_validation', 10, 2);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		
		$plugin_public = new Sg_Woocommerce_Order_Approval_With_Attachment_Public( $this->get_plugin_name(), $this->get_version() );

		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		


		$this->loader->add_filter( 'woocommerce_available_payment_gateways',$plugin_public,'oawoo_paymentgatways_disable_manager' ,1,1);
		$this->loader->add_filter( 'woocommerce_available_payment_gateways',$plugin_public,'attach_paymentgatways_disable_manager' ,10,1);

		$this->loader->add_filter( 'woocommerce_after_order_notes' , $plugin_public, 'sg_image_upload');
		$this->loader->add_action( 'init', $plugin_public, 'sg_attachment_order_id_set_cookie' );
		// add upload option in checkout page
		
		// add attached file details to thank you page and view order
		$this->loader->add_action( 'woocommerce_thankyou',$plugin_public, 'sg_attach_view_order_and_thankyou_page', 20 ,1);
		$this->loader->add_action( 'woocommerce_view_order',$plugin_public, 'sg_attach_view_order_and_thankyou_page', 20 ,1);
 
	
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sg_Woocommerce_Order_Approval_With_Attachment_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
