<?php
/**
 * Subscriptions Email Class
 *
 * Modifies the base WooCommerce email class and extends it to send subscription emails.
 *
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_Subscriptions_Email
 * @category	Class
 * @author		Prospress
 */
class WC_Subscriptions_Email {

	private static $woocommerce_email;

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 *
	 * @since 1.0
	 */
	public static function init() {

		add_action( 'woocommerce_email_classes', __CLASS__ . '::add_emails', 10, 1 );

		add_action( 'woocommerce_init', __CLASS__ . '::hook_transactional_emails' );

		add_filter( 'woocommerce_resend_order_emails_available', __CLASS__ . '::renewal_order_emails_available', -1 ); // run before other plugins so we don't remove their emails

		add_action( 'woocommerce_subscriptions_email_order_details', __CLASS__ . '::order_details', 10, 4 );
	}

	/**
	 * Add Subscriptions' email classes.
	 *
	 * @since 1.4
	 */
	public static function add_emails( $email_classes ) {

		require_once( 'emails/class-wcs-email-new-renewal-order.php' );
		require_once( 'emails/class-wcs-email-new-switch-order.php' );
		require_once( 'emails/class-wcs-email-customer-processing-renewal-order.php' );
		require_once( 'emails/class-wcs-email-customer-completed-renewal-order.php' );
		require_once( 'emails/class-wcs-email-customer-completed-switch-order.php' );
		require_once( 'emails/class-wcs-email-customer-renewal-invoice.php' );
		require_once( 'emails/class-wcs-email-cancelled-subscription.php' );
		require_once( 'emails/class-wcs-email-expired-subscription.php' );
		require_once( 'emails/class-wcs-email-on-hold-subscription.php' );

		$email_classes['WCS_Email_New_Renewal_Order']        = new WCS_Email_New_Renewal_Order();
		$email_classes['WCS_Email_New_Switch_Order']         = new WCS_Email_New_Switch_Order();
		$email_classes['WCS_Email_Processing_Renewal_Order'] = new WCS_Email_Processing_Renewal_Order();
		$email_classes['WCS_Email_Completed_Renewal_Order']  = new WCS_Email_Completed_Renewal_Order();
		$email_classes['WCS_Email_Completed_Switch_Order']   = new WCS_Email_Completed_Switch_Order();
		$email_classes['WCS_Email_Customer_Renewal_Invoice'] = new WCS_Email_Customer_Renewal_Invoice();
		$email_classes['WCS_Email_Cancelled_Subscription']   = new WCS_Email_Cancelled_Subscription();
		$email_classes['WCS_Email_Expired_Subscription']     = new WCS_Email_Expired_Subscription();
		$email_classes['WCS_Email_On_Hold_Subscription']     = new WCS_Email_On_Hold_Subscription();

		return $email_classes;
	}

	/**
	 * Hooks up all of Subscription's transaction emails after the WooCommerce object is constructed.
	 *
	 * @since 1.4
	 */
	public static function hook_transactional_emails() {

		// Don't send subscription
		if ( WC_Subscriptions::is_duplicate_site() && ! defined( 'WCS_FORCE_EMAIL' ) ) {
			return;
		}

		add_action( 'woocommerce_subscription_status_updated', __CLASS__ . '::send_cancelled_email', 10, 2 );
		add_action( 'woocommerce_subscription_status_expired', __CLASS__ . '::send_expired_email', 10, 2 );
		add_action( 'woocommerce_customer_changed_subscription_to_on-hold', __CLASS__ . '::send_on_hold_email', 10, 2 );

		$order_email_actions = array(
			'woocommerce_order_status_pending_to_processing',
			'woocommerce_order_status_pending_to_completed',
			'woocommerce_order_status_pending_to_on-hold',
			'woocommerce_order_status_failed_to_processing',
			'woocommerce_order_status_failed_to_completed',
			'woocommerce_order_status_failed_to_on-hold',
			'woocommerce_order_status_completed',
			'woocommerce_generated_manual_renewal_order',
			'woocommerce_order_status_failed',
		);

		foreach ( $order_email_actions as $action ) {
			add_action( $action, __CLASS__ . '::maybe_remove_woocommerce_email', 9 );
			add_action( $action, __CLASS__ . '::send_renewal_order_email', 10 );
			add_action( $action, __CLASS__ . '::send_switch_order_email', 10 );
			add_action( $action, __CLASS__ . '::maybe_reattach_woocommerce_email', 11 );
		}
	}

	/**
	 * Init the mailer and call for the cancelled email notification hook.
	 *
	 * @param $subscription WC Subscription
	 * @since 2.0
	 */
	public static function send_cancelled_email( $subscription ) {
		WC()->mailer();

		if ( $subscription->has_status( array( 'pending-cancel', 'cancelled' ) ) && 'true' !== get_post_meta( $subscription->id, '_cancelled_email_sent', true ) ) {
			do_action( 'cancelled_subscription_notification', $subscription );
		}
	}

	/**
	 * Init the mailer and call for the expired email notification hook.
	 *
	 * @param $subscription WC Subscription
	 * @since 2.1
	 */
	public static function send_expired_email( $subscription ) {
		WC()->mailer();

		do_action( 'expired_subscription_notification', $subscription );
	}

	/**
	 * Init the mailer and call for the suspended email notification hook.
	 *
	 * @param $subscription WC Subscription
	 * @since 2.1
	 */
	public static function send_on_hold_email( $subscription ) {
		WC()->mailer();

		do_action( 'on-hold_subscription_notification', $subscription );
	}

	/**
	 * Init the mailer and call the notifications for the renewal orders.
	 *
	 * @param int $user_id The ID of the user who the subscription belongs to
	 * @param string $subscription_key A subscription key of the form created by @see self::get_subscription_key()
	 * @return void
	 */
	public static function send_renewal_order_email( $order_id ) {
		WC()->mailer();

		if ( wcs_order_contains_renewal( $order_id ) ) {
			do_action( current_filter() . '_renewal_notification', $order_id );
		}
	}

	/**
	 * If the order is a renewal order, don't send core emails.
	 *
	 * @param int $user_id The ID of the user who the subscription belongs to
	 * @param string $subscription_key A subscription key of the form created by @see self::get_subscription_key()
	 * @return void
	 */
	public static function maybe_remove_woocommerce_email( $order_id ) {
		if ( wcs_order_contains_renewal( $order_id ) || wcs_order_contains_switch( $order_id ) ) {
			remove_action( current_filter(), array( 'WC_Emails', 'send_transactional_email' ) );
		}
	}

	/**
	 * If the order is a renewal order, don't send core emails.
	 *
	 * @param int $user_id The ID of the user who the subscription belongs to
	 * @param string $subscription_key A subscription key of the form created by @see self::get_subscription_key()
	 * @return void
	 */
	public static function maybe_reattach_woocommerce_email( $order_id ) {
		if ( wcs_order_contains_renewal( $order_id ) || wcs_order_contains_switch( $order_id ) ) {
			add_action( current_filter(), array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
		}
	}

	/**
	 * If viewing a renewal order on the the Edit Order screen, set the available email actions for the order to use
	 * renewal order emails, not core WooCommerce order emails.
	 *
	 * @param int $user_id The ID of the user who the subscription belongs to
	 * @param string $subscription_key A subscription key of the form created by @see self::get_subscription_key()
	 * @return void
	 */
	public static function renewal_order_emails_available( $available_emails ) {
		global $theorder;

		if ( wcs_order_contains_renewal( $theorder->id ) ) {
			$available_emails = array(
				'new_renewal_order',
				'customer_processing_renewal_order',
				'customer_completed_renewal_order',
			);

			if ( $theorder->needs_payment() ) {
				array_push( $available_emails, 'customer_renewal_invoice' );
			}
		}

		return $available_emails;
	}

	/**
	 * Init the mailer and call the notifications for subscription switch orders.
	 *
	 * @param int $user_id The ID of the user who the subscription belongs to
	 * @param string $subscription_key A subscription key of the form created by @see self::get_subscription_key()
	 * @return void
	 */
	public static function send_switch_order_email( $order_id ) {
		WC()->mailer();

		if ( wcs_order_contains_switch( $order_id ) ) {
			do_action( current_filter() . '_switch_notification', $order_id );
		}
	}

	/**
	 * Generate an order items table using a WC compatible version of the function.
	 *
	 * @param object $order
	 * @param array $args {
	 *     @type bool  'show_download_links'
	 *     @type bool  'show_sku'
	 *     @type bool  'show_purchase_note'
	 *     @type array 'image_size'
	 *     @type bool  'plain_text'
	 * }
	 * @return string email order items table html
	 */
	public static function email_order_items_table( $order, $args = array() ) {
		$items_table = '';

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		if ( is_a( $order, 'WC_Abstract_Order' ) ) {

			if ( WC_Subscriptions::is_woocommerce_pre( '2.5' ) ) {

				$items_table = call_user_func_array( array( $order, 'email_order_items_table' ), $args );
			} else {

				// 2.5 doesn't support both the show_download_links or show_purchase_note parameters but uses $order->is_download_permitted and  $order->is_paid instead
				$show_download_links_callback = ( isset( $args['show_download_links'] ) && $args['show_download_links'] ) ? '__return_true' : '__return_false';
				$show_purchase_note_callback  = ( isset( $args['show_purchase_note'] ) && $args['show_purchase_note'] ) ? '__return_true' : '__return_false';

				unset( $args['show_download_links'] );
				unset( $args['show_purchase_note'] );

				add_filter( 'woocommerce_order_is_download_permitted', $show_download_links_callback );
				add_filter( 'woocommerce_order_is_paid', $show_purchase_note_callback );

				$items_table = $order->email_order_items_table( $args );

				remove_filter( 'woocommerce_order_is_download_permitted', $show_download_links_callback );
				remove_filter( 'woocommerce_order_is_paid', $show_purchase_note_callback );
			}
		}

		return $items_table;
	}

	/**
	 * Show the order details table
	 *
	 * @param WC_Order $order
	 * @param bool $sent_to_admin Whether the email is sent to admin - defaults to false
	 * @param bool $plain_text Whether the email should use plain text templates - defaults to false
	 * @param WC_Email $email
	 * @since 2.1
	 */
	public static function order_details( $order, $sent_to_admin = false, $plain_text = false, $email = '' ) {

		$order_items_table_args = array(
			'show_download_links' => ( $sent_to_admin ) ? false : $order->is_download_permitted(),
			'show_sku'            => $sent_to_admin,
			'show_purchase_note'  => ( $sent_to_admin ) ? false : $order->has_status( apply_filters( 'woocommerce_order_is_paid_statuses', array( 'processing', 'completed' ) ) ),
			'show_image'          => '',
			'image_size'          => '',
			'plain_text'          => $plain_text,
		);

		$template_path = ( $plain_text ) ? 'emails/plain/email-order-details.php' : 'emails/email-order-details.php';
		$order_type    = ( wcs_is_subscription( $order ) ) ? 'subscription' : 'order';

		wc_get_template(
			$template_path,
			array(
				'order'                  => $order,
				'sent_to_admin'          => $sent_to_admin,
				'plain_text'             => $plain_text,
				'email'                  => $email,
				'order_type'             => $order_type,
				'order_items_table_args' => $order_items_table_args,
			),
			'',
			plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/'
		);
	}

	/**
	 * Init the mailer and call the notifications for the current filter.
	 *
	 * @param int $user_id The ID of the user who the subscription belongs to
	 * @param string $subscription_key A subscription key of the form created by @see self::get_subscription_key()
	 * @return void
	 * @deprecated 2.0
	 */
	public static function send_subscription_email( $user_id, $subscription_key ) {
		_deprecated_function( __FUNCTION__, '2.0' );
	}
}

WC_Subscriptions_Email::init();
