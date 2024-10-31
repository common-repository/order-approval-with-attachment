<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sg_Woocommerce_Order_Approval_With_Attachment
 * @subpackage Sg_Woocommerce_Order_Approval_With_Attachment/admin
 * @author     Sevengits <support@sevengits.com>
 */
class Sg_Woocommerce_Order_Approval_With_Attachment_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style('chosen', plugin_dir_url(__FILE__) . 'css/chosen.min.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script('sg-attachment', plugin_dir_url(__FILE__) . 'js/sg-woocommerce-order-approval-with-attachment-admin.js', array('chosen'), $this->version, true);
		wp_enqueue_script('chosen', plugin_dir_url(__FILE__) . 'js/chosen.jquery.min.js', array('jquery'), $this->version, false);
	}
	/**
	 * add custom status
	 * @since    1.0.0
	 */
	function oawoo_register_my_new_order_statuse_wc_waiting()
	{

		register_post_status('wc-waiting', array(
			'label'                     => _x('Waiting for approval', 'Order status', 'order-approval-with-attachment'),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop('Waiting approval <span class="count">(%s)</span>', 'Waiting<span class="count">(%s)</span>', 'order-approval-with-attachment')
		));
	}

	/**
	 * add custom status
	 * @since    1.0.0
	 
	 */

	function oawoo_wc_order_statuse_wc_waiting($order_statuses)
	{
		$order_statuses['wc-waiting'] = _x('Waiting for approval', 'Order status', 'order-approval-with-attachment');

		return $order_statuses;
	}
	/**
	 * @since 1.0.0  add action button in admin 
	 * @return actions
	 */
	function oawoo_add_custom_order_status_actions_button($actions, $order)
	{

		if ($order->has_status(array('waiting'))) {

			$actions['wc_approved'] = array(
				'url'       => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=pending&order_id=' . $order->get_id()), 'woocommerce-mark-order-status'),
				'name'      => __('Approve', 'order-approval-with-attachment'),
				'action'    => 'wc_approved',
			);
			// Set the action button
			$actions['wc_reject'] = array(
				'url'       => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=cancelled&order_id=' . $order->get_id()), 'woocommerce-mark-order-status'),
				'name'      => __('Reject', 'order-approval-with-attachment'),
				'action'    => 'wc_reject',
			);
		}

		return $actions;
	}
	/**
	 * 
	 * @since 1.0.0  add action button in admin 
	 
	 */


	function oawoo_add_custom_order_status_actions_button_css()
	{

		echo '<style>.wc-action-button-wc_approved::after { font-family: woocommerce !important; content: "\e015" !important; color:GREEN }</style>';
		echo '<style>.wc-action-button-wc_reject::after { font-family: woocommerce !important; content: "\e013" !important; color:RED}</style>';
	}
	/**
	 * @since 1.0.0  
	 * Add the gateway to WC Available Gateways
	 */

	function oawoo_wc_add_to_gateways($gateways)
	{

		$gateways[] = 'Sgit_Attach_Gateway';
		return $gateways;
	}

	/**
	 *  @since 1.0.0  
	 * Adds plugin page links
	 */

	function oawoo_wc_gateway_plugin_links($links)
	{

		$plugin_links = array(
			'<a href="' . admin_url('admin.php?page=wc-settings&tab=advanced&section=sg_order_attach_tab') . '">' . __('Settings', 'order-approval-with-attachment') . '</a>',


		);

		return array_merge($plugin_links, $links);
	}

	/**
	 *  @since 1.0.0  
	 * 
	 */
	function oawoo_wc_gateway_init()
	{

		require_once SGOAA_PLUGIN_PATH_ORDER_ATTACHMENT . 'includes/class-sg-order-approval-woocommerce-pro-payment-gateway.php';
	}
	/**
	 *  @since 1.0.0  
	 * 
	 */

	function email_waiting_new_order_notifications($order_id, $order)
	{

		WC()->mailer()->get_emails()['Sgoaa_WC_Customer_Order_New']->trigger($order_id);
		WC()->mailer()->get_emails()['Sgoaa_WC_Admin_Order_New']->trigger($order_id);
	}

	/**
	 *  @since 1.0.0  
	 * 
	 */

	function oawoo_status_waiting_rejected_notification($order_id, $order)
	{

		WC()->mailer()->get_emails()['Sgoaa_WC_Customer_Order_Rejected']->trigger($order_id);
	}


	/**
	 *  @since 1.0.0  
	 * 
	 */

	function oawoo_status_waiting_approved_notification($order_id, $order)
	{

		WC()->mailer()->get_emails()['Sgoaa_WC_Customer_Order_Approved']->trigger($order_id);
	}

	/**
	 *	@since 1.0.0 
	 */
	public function sg_get_settings($settings, $current_section)
	{

		$custom_settings = array();

		if ('sg_order_attach_tab' == $current_section) {

			$custom_settings =  array(

				array(
					'name' => __('Sg Woocommerce Order Approval with Attachment', 'order-approval-with-attachment'),
					'type' => 'title',
					'desc' => __('Add global settings below. Goto payment gateway and enable Pre-order gateway.', 'order-approval-with-attachment'),
					'id'   => 'sg_tab_main_order'
				),
				array(
					'name' => __('Enable order approval with attachment', 'order-approval-with-attachment'),
					'id' 			=> 'sg_attach_enable_order_approval',
					'type'			=> 'radio',

					'options'		=> array('enable' => 'For all Products', 'disable' => 'Per Product'),
					'default'		=> 'enable',

				),
				array(
					"name"    => __('Enable orders editable', 'order-approval-with-attachment'),
					'id'    => 'sg_enable_order_edit',
					"type"    => "checkbox",
					'desc' => __('Admin can edit order when new orders created', 'order-approval-with-attachment'),
					'desc_tip' => false,
				),
			
				array(
					"name"    => __('Delete Attachment', 'order-approval-with-attachment'),
					'id'    => 'sg_del_attachment',
					"type"    => "checkbox",
					'desc' => __('Delete attachments associated with the order when order is trashed ', 'order-approval-with-attachment'),
					'desc_tip' => false,
				),
				array(
					'name' => __('Attachment upload label', 'order-approval-with-attachment'),
					'type' => 'text',
					'id'	=> 'sg_attach_upload_label',
					'desc' => __('label inside upload field', 'order-approval-with-attachment'),
					'desc_tip' => true,
					'default'=>'Click here or Drop files here',
					'placeholder'=>'Click here or Drop files here'

				),
				array(
					'name' => __('Attachment Field Label', 'order-approval-with-attachment'),
					'type' => 'text',
					'id'	=> 'sg_attach_label',
					'desc' => __('title for attachment field', 'order-approval-with-attachment'),
					'desc_tip' => true,
					'placeholder'=>'attach a file'

				),
				
				array('type' => 'sectionend', 'id' => 'order_attach_woo'),

			);

			return $custom_settings;
		} else {
			return $settings;
		}
	}
	/**
	 *	@since 1.0.0 
	 */

	public function sg_add_settings_tab($settings_tab)
	{
		$settings_tab['sg_order_attach_tab'] = __('SG Order Approval with Attachment');
		return $settings_tab;
	}
	/**
	 * @since 1.0.0 add checkbox in product general tab
	 */

	function sg_preorder_check_product_options()
	{

		echo '<div class="options_group">';

		woocommerce_wp_checkbox(array(
			'id'      => 'sg_product_attach_enable',
			'value'   => get_post_meta(get_the_ID(), 'sg_product_attach_enable', true),
			'label'   => 'SG Attachment',
			'desc_tip' => false,
			'description' => 'This product requires an attachment file and order should be approved by admin/shop owner. Payment will not be taken during checkout.',
		));

		echo '</div>';
	}

	/** 
	 * @since 1.0.0  save field
	 */

	function sg_save_fields($id, $post)
	{
		if (isset($_POST['sg_product_attach_enable'])) {
			$sg_product_enable = 'yes';
		} else {
			$sg_product_enable = 'no';
		}

		update_post_meta($id, 'sg_product_attach_enable', $sg_product_enable);
	}

	function wc_make_processing_orders_editable($is_editable, $order)
	{


		if ($order->get_status() == 'waiting') {
			$is_editable = true;
		}

		return $is_editable;
	}
	/**
	 * This function will return a custom upload directory details.
	 *
	 * @param [array] $dirs
	 * @return [array] 
	 * @since 1.0.0
	 */

	function sg_183245_upload_dir( $dirs ) {

		if(isset($_COOKIE['sg_order_temp'])){
			$temp_folder_name=sanitize_file_name($_COOKIE['sg_order_temp']);
		}else{
			$temp_folder_name='temp';
		}
		
		$dirs['subdir'] = '/sg-attachments/'.date("Y")."/".date("m")."/";
		$dirs['path'] = $dirs['basedir'] .$dirs['subdir'].$temp_folder_name;
		$dirs['url'] = $dirs['baseurl'] . $dirs['subdir'].$temp_folder_name;
	
		return $dirs;
	}
	/**
	 * Image upload processing function 
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function sg_imageupload_checkout_field_process()
	{
		
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		
		/* First, check nonce */
		check_ajax_referer( 'sg_image_checkout', 'nonce_data' );

		$upload_overrides = array( 'test_form' => false );
		$get_selected_keys = [];
		$response=array();
		$errors     = array();
		$maxsize    = 2097152;
		
		$acceptable = array(
				'image/jpeg',
				'image/jpg',
			);
		
		$count=1;
		foreach ($_FILES['sg_attachments']['name'] as $key => $value) {
			
			$count++;
			// sanitise files 
			
			if(($_FILES['sg_attachments']['size'][$key] >= $maxsize) || ($_FILES["sg_attachments"]["size"][$key] == 0)) {
				$errors[] = 'File too large. File must be less than 2 megabytes.';
			}

			if(!in_array($_FILES['sg_attachments']['type'][$key], $acceptable) && (!empty($_FILES["sg_attachments"]["type"][$key]))) {
				$errors[] = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
			}
			if(count($errors) === 0) {
					$all_attachments = array(
						'name' => sanitize_file_name($_FILES['sg_attachments']['name'][$key]),
						'type' =>sanitize_mime_type( $_FILES['sg_attachments']['type'][$key] ) ,
						'tmp_name' =>(string)$_FILES['sg_attachments']['tmp_name'][$key],
						'error' =>intval($_FILES['sg_attachments']['error'][$key]),
						'size' => intval($_FILES['sg_attachments']['size'][$key])
					);
			}		
			
		}
				
		
		if(isset($_REQUEST['selected_imgs'])){
			foreach ($_REQUEST['selected_imgs'] as $img) {
				$sel_imgs[] = sanitize_file_name($img);
			}
		}else{
			$sel_imgs=array();
		}
		
		// temporarly change upload directory to sg-attachments
		add_filter( 'upload_dir', array($this,'sg_183245_upload_dir' )); // important 
		
		foreach($all_attachments as $key => $attachment) {
			if(empty($sel_imgs)){
				break;
			}
			foreach ($sel_imgs as $key1 => $value) {
				if (isset($attachment) && isset($value) && $attachment === $value) {
					array_push($get_selected_keys,  $key);
				}
			}
		}

		foreach($get_selected_keys as $key => $sel_key) {
			$file = array(
							'name' => $all_attachments['name'],
							'type' => $all_attachments['type'],
							'tmp_name' =>$all_attachments['tmp_name'],
							'error' => $all_attachments['error'],
							'size' => $all_attachments['size']
						);

			
			$upload_dir = wp_upload_dir();
			$current_file= $upload_dir['path']."/".$all_attachments['name'];
			
			// make sure that we delete file only inside sg_attachment folder to ensure no other upload directry affected
			if (strpos($current_file, 'sg-attachments') !== false) {
				
				wp_delete_file($current_file);
			}
			//save files	
			$movefile = wp_handle_upload( $file, $upload_overrides );

			if( $movefile && ! isset( $movefile['error'] ) ) {
				$response = array( 'success' => true );  
				
			}else {
				
				wp_send_json( $movefile['error'],200 );
				$response = array( 
					'success' => true,
					'error'=>$movefile['error'] 
				);  
				
			}
					

		}
			// remove all files in current load directory and keep only selected files.
				$upload_dir = wp_upload_dir();
				//The name of the folder.
				$folder = $upload_dir['path'];
		
				//Get a list of all of the file names in the folder.
				$files = glob($folder . '/*');
				
				//Loop through the file list.
				foreach($files as $file){
			//Make sure that this is a file and not a directory.
					if(is_file($file) && !in_array(basename($file),$sel_imgs)){
						
						// make sure that we delete file only inside sg_attachment folder to ensure no other upload directry affected
						if (strpos($file, 'sg-attachments') !== false) {
							wp_delete_file($file);
						}
					}
				}
			remove_filter( 'upload_dir', array($this,'sg_183245_upload_dir') );// important
			wp_send_json( $response,200 );
	}
	/**
	 * change temporary folder name to order id.
	 *
	 * @param [int] $order_id
	 * @return void
	 * @since 1.0.0
	 */

	function sg_change_folder_name($order_id){

			add_filter( 'upload_dir', array($this,'sg_183245_upload_dir' ));
			$upload_dir = wp_upload_dir();
			$temp_folder_name= sanitize_file_name($_COOKIE['sg_order_temp']);
			rename($upload_dir['path'],$upload_dir['basedir']."/".$upload_dir['subdir'].$order_id);
			remove_filter( 'upload_dir', array($this,'sg_183245_upload_dir') );	
			// Remove Cookie
			if (isset($_COOKIE['sg_order_temp'])) {
				unset($_COOKIE['sg_order_temp']);
				setcookie('sg_order_temp', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN); // empty value and old timestamp
			}
	}
	/**
	 * Add metabox area in edit order screen
	 *	@since 1.0.0
	 * @return void
	 */
    function sg_attachment_add_meta_boxes()
    {
        add_meta_box( 'sg_attachment_metabox', __('Sg Order Attachments','woocommerce'), array($this,'sg_add_order_attachments') , 'shop_order', 'normal', 'default' );
    }
	/**
	 *	Show uploaded files details in order edit page
	 *	@since 1.0.0
	 * 	@return void
	 */
	public function sg_add_order_attachments()
    {
		global $woocommerce, $post;
		$order = new WC_Order($post->ID);
		$order_id=$post->ID;
		$upload_dir = wp_upload_dir();
      	$order_attachment_folder= $upload_dir['basedir']."/sg-attachments".$upload_dir['subdir']."/".$order_id;
		$order_attachment_folder_url= $upload_dir['baseurl']."/sg-attachments".$upload_dir['subdir']."/".$order_id; 
		
		
		$files =list_files($order_attachment_folder,2);
		if(empty($files)){
			_e('No files found', 'order-approval-with-attachment');
		}
		
		echo "<table>";
		$count=1;
			foreach ($files as $file) {
				if ( is_file( $file ) ){
					$filesize = size_format( filesize( $file ) );
					$filename = basename($file); 
				}
				$file_url=$order_attachment_folder_url."/".$filename;
				echo "<tr><td>".$count."</td>";
				echo "<td><a href=".$file_url." target='_blank'>".ucfirst($filename)."</a></td>";
				echo "<td>".$filesize."</td></tr>";
				$count++;
			}
		echo "</table>";

	
    }

	function sg_attachment_action_woocommerce_trash_order($id){
			$post_type = get_post_type($id);

			if($post_type !== 'shop_order') {
				return;
			}
		// return if delete attachement not enable in settings
		
		if(get_option('sg_del_attachment')=='no'){
			return;
		} 

		$upload_dir = wp_upload_dir();
      	$order_attachment_folder= $upload_dir['basedir']."/sg-attachments".$upload_dir['subdir']."/".$id;
		$files = glob($order_attachment_folder . '/*');
		//Loop through the file list.
		foreach($files as $file){
			//Make sure that this is a file and not a directory.
					if(is_file($file)){
						// make sure that we delete file only inside sg_attachment folder to ensure no other upload directry affected
						if (strpos($file, 'sg-attachments') !== false) {
							wp_delete_file($file);
						}
					}
		}

		if (is_dir($order_attachment_folder)) {		
			rmdir($order_attachment_folder);
		}

		

	}

/**
 * when attachment field is present on checkout page make sure that user is upload atleast 1 files to attachment folder
 * also folder name is in cookie because order is not yet created.
 *
 * @param [array] $fields
 * @param [array] $errors
 * @return void
 */
	function sg_checkout_attachment_validation( $fields, $errors ){
		
		if (isset($_COOKIE['sg_order_temp'])) {
			$folder_name=sanitize_file_name($_COOKIE['sg_order_temp']);
		}
		if($this->sg_active_payment_gateway_is_sgitsoa_gateway()==false){
			return;
		}
		$upload_dir = wp_upload_dir();
      	$order_attachment_folder= $upload_dir['basedir']."/sg-attachments".$upload_dir['subdir']."/".$folder_name;
		$files = glob($order_attachment_folder . '/*');
		if(empty($files)){
			$errors->add( 'attach-validation', 'Please upload files to proceed checkout.' );
		}
		

	}

	function sg_active_payment_gateway_is_sgitsoa_gateway(){

		$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
		if(isset($available_payment_methods['sgitattach_gateway'])){
			$sgitsoa_gateway=true;
		}else{
			$sgitsoa_gateway=false;
		}
		return $sgitsoa_gateway;
	}

}
