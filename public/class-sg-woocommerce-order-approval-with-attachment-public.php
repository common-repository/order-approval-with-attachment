<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sg_Woocommerce_Order_Approval_With_Attachment
 * @subpackage Sg_Woocommerce_Order_Approval_With_Attachment/public
 * @author     Sevengits <support@sevengits.com>
 */
class Sg_Woocommerce_Order_Approval_With_Attachment_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/sg-woocommerce-order-approval-with-attachment-public.css', array(), $this->version, 'all');
		//	wp_enqueue_style( 'sg-imageuploader-css', plugin_dir_url( __FILE__ ) . 'css/image-uploader.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{



		wp_enqueue_script('sg-attachment-public', plugin_dir_url(__FILE__) . 'js/sg-woocommerce-order-approval-with-attachment-public.js', array('jquery'), $this->version, false);
		
		wp_localize_script(
			'sg-attachment-public',
			'sg_attach_vars',
			array(
				'sg_allowed_files' => array('image/jpeg'),
				'sg_max_size' => 1
			)
		);
	}
	function oawoo_paymentgatways_disable_manager($available_gateways)
	{
		global $woocommerce;
		$allowed_gateways  = array();
		$sg_product_enable = false;


		if (is_admin()) {
			return $available_gateways;
		}


		// check cart for any product which enable product approval

		if (get_option('sg_attach_enable_order_approval') == 'disable') { // plugin activate for per product


			$items = $woocommerce->cart->get_cart();

			foreach ($items as $item => $values) {
				$_product =  wc_get_product($values['data']->get_id());


				if (get_post_meta($values['product_id'], 'sg_product_attach_enable', true) == 'yes') {
					$sg_product_enable = true;
				}
			}
		}



		// Check all products in cart. If any of the product require confirmation please make order approval.



		if (get_option('sg_attach_enable_order_approval') == 'enable' || $sg_product_enable == true) {

			if (is_checkout() && isset($available_gateways['sgitattach_gateway']) && !is_wc_endpoint_url('order-pay')) {

				$allowed_gateways['sgitattach_gateway'] = $available_gateways['sgitattach_gateway'];
				return $allowed_gateways;
			}

			if (is_wc_endpoint_url('order-pay') && isset($available_gateways['sgitattach_gateway'])) {

				unset($available_gateways['sgitattach_gateway']);
				return $available_gateways;
			}
		}
		//

		if ($sg_product_enable == false) {
			unset($available_gateways['sgitattach_gateway']);
		}

		return $available_gateways;
	}

	function sg_image_upload()
	{
		if($this->sg_active_payment_gateway_is_sgitsoa_gateway()==false){
			return;
		}
?>
		<div class="sg-attachment-product-image-section">
			<div class="sg-attachment-uploader-title"><?php echo get_option('sg_attach_label');   ?></div>
			<div class="sg-attachment-img-uploader">
				<input type="hidden" id="sg_attachment_ajax_caller" value="<?php echo admin_url('admin-ajax.php'); ?>">
				<input type="hidden" id="sg_nonce" data-nonce="<?php echo wp_create_nonce('sg_image_checkout'); ?>">
				<input type="file" multiple="true" name="sg_order_attachments[]" id="sg_order_attachments">
				<div class="sgoa-panel-content">
					<div class="img-responsive"><img src="<?php echo plugin_dir_url(__DIR__) . 'public/img/file_upload.png'; ?>" alt=""></div>
					<label class="sg-file-upload-btn" for="sg_order_attachments"><?php echo get_option('sg_attach_upload_label', 'click here or drop here'); ?></label>
				</div>

			</div>
			<div class="sg-order-attachment-hidden-fields">
			</div>
			<div class="sg-attachment-img-previewer"></div>
		</div>

<?php
	}
	/**
	 * @since 1.0.0
	 * set  temp folder name as cookie
	 * 
	 */

	function sg_attachment_order_id_set_cookie()
	{


		if (!isset($_COOKIE['sg_order_temp'])) {
			setcookie('sg_order_temp', uniqid('sg_'), strtotime('+1 day'), COOKIEPATH, COOKIE_DOMAIN);
		}
	}
	/**
	 * 
	 *	Disply file info in thank you page and order view page myaccount.
	 * @param [int] $order_id
	 * @return void
	 * @since 1.0.0
	 */
	function sg_attach_view_order_and_thankyou_page($order_id)
	{

		$upload_dir = wp_upload_dir();
		$order_attachment_folder = $upload_dir['basedir'] . "/sg-attachments" . $upload_dir['subdir'] . "/" . $order_id;
		$order_attachment_folder_url = $upload_dir['baseurl'] . "/sg-attachments" . $upload_dir['subdir'] . "/" . $order_id;


		$files = glob($order_attachment_folder . "/*");
		if (empty($files)) {
			//_e('No files found', 'order-approval-with-attachment');
		}
		echo '<div id="label_attach"><h4>' . esc_html__('Uploaded Files', 'order-approval-with-attachment') . '</h4></div>';
		echo "<table>";
		echo "<tr>";
		echo '<th>' . esc_html__('Sl No', 'order-approval-with-attachment') . '</th>';
		echo '<th>' . esc_html__('File Name', 'order-approval-with-attachment') . '</th>';
		echo '<th>' . esc_html__('Size', 'order-approval-with-attachment') . '</th>';
		echo "</tr>";
		$count = 1;
		foreach ($files as $file) {
			if (is_file($file)) {
				$filesize = size_format(filesize($file));
				$filename = basename($file);
			}
			$file_url = $order_attachment_folder_url . "/" . $filename;
			echo "<tr><td>" . $count . "</td>";
			echo "<td><a href=" . $file_url . " target='_blank'>" . ucfirst($filename) . "</a></td>";
			echo "<td>" . $filesize . "</td></tr>";
			$count++;
		}
		echo "</table>";
	}
 function sg_active_payment_gateway_is_sgitsoa_gateway(){
		
		$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
		
		if(isset($available_payment_methods['sgitattach_gateway'])  ){
			$sgitsoa_gateway=true;
		}else{
			$sgitsoa_gateway=false;
		}
		return $sgitsoa_gateway;
	}
	function attach_paymentgatways_disable_manager($available_gateways){
		global $woocommerce;
		$allowed_gateways  = array();
		$sg_product_enable = false;


		if (is_admin()) {
			return $available_gateways;
		}


		
			$items = $woocommerce->cart->get_cart();

			foreach ($items as $item => $values) {
				$_product =  wc_get_product($values['data']->get_id());


				if (get_post_meta($values['product_id'], 'sg_product_attach_enable', true) == 'yes') {
					$sg_product_enable = true;
				}
			}
		
		// if order approval free version installed	
		if ($sg_product_enable == true && isset($available_gateways['woa_gateway'])) {
			unset($available_gateways['woa_gateway']);
		}
		if ($sg_product_enable == true && isset($available_gateways['sgitsoa_gateway'])) {
			unset($available_gateways['sgitsoa_gateway']);
		}


		return $available_gateways;
	}

}
