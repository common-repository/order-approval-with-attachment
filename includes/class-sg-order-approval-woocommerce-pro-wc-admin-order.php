<?php

/**
 * Class Sgoaa_WC_Admin_Order_New
 */
class Sgoaa_WC_Admin_Order_New  extends WC_Email
{

	/**
	 * Create an instance of the class.
	 *
	 * @access public
	 * @return void
	 */
	function __construct()
	{
		// Email slug we can use to filter other data.
		$this->id          = 'sgoaa_wc_admin_order_new';
		$this->title       = esc_html__('Admin Pre order notification ', 'order-approval-with-attachment');
		$this->description = esc_html__('An email sent to the admin when an order is created.', 'order-approval-with-attachment');
		// default the email recipient to the admin's email address

		$this->recipient = $this->get_option('recipient');

		// if none was entered, just use the WP admin email as a fallback
		if (!$this->recipient)
			$this->recipient = get_option('admin_email');
		$this->heading     = esc_html__('New Order ', 'order-approval-with-attachment');
		// translators: placeholder is {blogname}, a variable that will be substituted when email is sent out
		$this->subject     = sprintf(esc_html_x('[%s] : New Order #[%s]', 'default email subject for new emails sent to admin', 'order-approval-with-attachment'), '{blogname}', '{order_number}');

		// Template paths.
		$this->template_html  = 'emails/wc-admin-order-new.php';
		$this->template_plain = 'emails/plain/wc-admin-order-new.php';
		if(file_exists(get_stylesheet_directory().'/woocommerce/'.$this->template_html) ){
			$this->template_base  = get_stylesheet_directory().'/woocommerce/';
		}elseif(file_exists(get_stylesheet_directory().'/woocommerce/'.$this->template_plain)){
			$this->template_base  = get_stylesheet_directory().'/woocommerce/';
		}else{
			$this->template_base  = SGOAA_PLUGIN_PATH_ORDER_ATTACHMENT . 'templates/';
		} 


		parent::__construct();
	}

	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 1.0.0
	 * @param int $order_id
	 */
	public function trigger($order_id)
	{

		// bail if no order ID is present
		if (!$order_id)
			return;

		// setup order object
		$this->object = new WC_Order($order_id);


		// replace variables in the subject/headings
		$this->find[] = '{order_date}';
		$this->replace[] = date_i18n(wc_date_format(), strtotime($this->object->get_date_created()));

		$this->find[] = '{order_number}';
		$this->replace[] = $this->object->get_order_number();

		if (!$this->is_enabled() || !$this->get_recipient())
			return;

		// woohoo, send the email!
		$this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
	}
	/**
	 * get_content_html function.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_content_html()
	{
		ob_start();
		wc_get_template($this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'email'			=> $this,
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin' => true,
			'plain_text'    => false,
		), $this->template_base, $this->template_base);
		return ob_get_clean();
	}


	/**
	 * get_content_plain function.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_content_plain()
	{
		ob_start();
		wc_get_template($this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'email'			=> $this,
			'sent_to_admin' => true,
			'plain_text'    => false,
		));
		return ob_get_clean();
	}
	// form fields that are displayed in WooCommerce->Settings->Emails
	function init_form_fields()
	{
		$this->form_fields = array(
			'enabled' => array(
				'title' 		=> esc_html__('Enable/Disable', 'order-approval-with-attachment'),
				'type' 			=> 'checkbox',
				'label' 		=> esc_html__('Enable this email notification', 'order-approval-with-attachment'),
				'default' 		=> 'yes'
			),
			'recipient' => array(
				'title'         => esc_html__('Recipient', 'order-approval-with-attachment'),
				'type'          => 'text',
				'description'   => sprintf(esc_html__('Enter recipients (comma separated) for this email. Defaults to %s', 'order-approval-with-attachment'), get_option('admin_email')),
				'default'       => get_option('admin_email')
			),
			'subject' => array(
				'title' 		=> esc_html__('Subject', 'order-approval-with-attachment'),
				'type' 			=> 'text',
				'description' 	=> sprintf(esc_html__('This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'order-approval-with-attachment'), $this->subject),
				'placeholder' 	=> esc_attr(''),
				'default' 		=> esc_attr('')
			),
			'heading' => array(
				'title' 		=> esc_html__('Email Heading', 'order-approval-with-attachment'),
				'type' 			=> 'text',
				'description' 	=> sprintf(esc_html__('This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'order-approval-with-attachment'), $this->heading),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'email_type' => array(
				'title' 		=> esc_html__('Email type', 'order-approval-with-attachment'),
				'type' 			=> 'select',
				'description' 	=> esc_html__('Choose which format of email to send.', 'order-approval-with-attachment'),
				'default' 		=> 'html',
				'class'			=> 'email_type',
				'options'		=> array(
					'plain'		 	=> esc_attr__('Plain text', 'order-approval-with-attachment'),
					'html' 			=> esc_attr__('HTML', 'order-approval-with-attachment'),
					'multipart' 	=> esc_attr__('Multipart', 'order-approval-with-attachment'),
				)
			)
		);
	}
}
