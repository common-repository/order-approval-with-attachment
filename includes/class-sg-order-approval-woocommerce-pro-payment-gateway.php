<?php

/**
 * 
 *
 * @link       https://sevengits.com/
 * @since      1.0.0
 *
 * @package    Sg_Order_Approval_Woocommerce_Pro
 * @subpackage Sg_Order_Approval_Woocommerce_Pro/includes
 */

/**
 * Payment Gateway for Order Approval.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sg_Order_Approval_Woocommerce_Pro
 * @subpackage Sg_Order_Approval_Woocommerce_Pro/includes
 * @author     Sevengits <sevengits@gmail.com>
 */
class Sgit_Attach_Gateway extends WC_Payment_Gateway
{

	/**
	 * Constructor for the gateway.
	 */
	public function __construct()
	{

		$this->id                 = 'sgitattach_gateway';
		$this->icon               = apply_filters('woocommerce_offline_icon', '');
		$this->has_fields         = false;
		$this->method_title       = esc_html__('Woocommerce Order Approval with Attachment', 'order-approval-with-attachment');
		$this->method_description = esc_html__('Allow customer to upload attachment and store owner need to approve order before payment.', 'order-approval-with-attachment');

		// Load the settings.
		$this->sgitsoa_init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title        = $this->get_option('title');
		$this->description  = $this->get_option('description');
		$this->instructions = $this->get_option('instructions', $this->description);

		// Actions
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_thankyou_' . $this->id, array($this, 'sgitsoa_thankyou_page'));

		// Customer Emails
		add_action('woocommerce_email_before_order_table', array($this, 'sgitsoa_email_instructions'), 10, 3);
	}


	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function sgitsoa_init_form_fields()
	{

		$this->form_fields = apply_filters('wc_offline_form_fields', array(

			'enabled' => array(
				'title'   => esc_html__('Enable/Disable', 'order-approval-with-attachment'),
				'type'    => 'checkbox',
				'label'   => esc_html__('Enable Woocommerce Order Approval Payment', 'order-approval-with-attachment'),
				'default' => 'yes'
			),

			'title' => array(
				'title'       => esc_html__('Title', 'order-approval-with-attachment'),
				'type'        => 'text',
				'description' => esc_html__('This controls the title for the payment method the customer sees during checkout.', 'order-approval-with-attachment'),
				'default'     => esc_html__('Pre Order', 'order-approval-with-attachment'),
				'desc_tip'    => true,
			),

			'description' => array(
				'title'       => esc_html__('Description', 'order-approval-with-attachment'),
				'type'        => 'textarea',
				'description' => esc_html__('Payment method description that the customer will see on your checkout.', 'order-approval-with-attachment'),
				'default'     => esc_attr__('Please remit payment after order approval.', 'order-approval-with-attachment'),
				'desc_tip'    => true,
			),

			'instructions' => array(
				'title'       => esc_html__('Instructions', 'order-approval-with-attachment'),
				'type'        => 'textarea',
				'description' => esc_html__('Instructions that will be added to the thank you page and emails.', 'order-approval-with-attachment'),
				'default'     => esc_html__('', 'order-approval-with-attachment'),
				'desc_tip'    => true,
			),
		));
	}


	/**
	 * Output for the order received page.
	 */
	public function sgitsoa_thankyou_page()
	{
		if ($this->instructions) {
			echo wpautop(wptexturize($this->instructions));
		}
	}


	/**
	 * Add content to the WC emails.
		
	 */
	public function sgitsoa_email_instructions($order, $sent_to_admin, $plain_text = false)
	{

		if ($this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method() && $order->has_status('waiting')) {
			echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
		}
	}


	/**
	 * Process the payment and return the result
		
	 */
	public function process_payment($order_id)
	{

		$order = wc_get_order($order_id);

		// Mark as waiting (we're awaiting the payment)
		$order->update_status('wc-waiting', esc_html__('waiting admin approval', 'order-approval-with-attachment'));

		// Reduce stock levels
		wc_reduce_stock_levels($order_id);

		// Remove cart
		WC()->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result' 	=> esc_html__('success', 'order-approval-with-attachment'),
			'redirect'	=> $this->get_return_url($order)
		);
	}
}
