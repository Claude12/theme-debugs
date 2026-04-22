<?php
/**
 * Template for email to admin.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/emails/quote-email-to-admin.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked AF_R_F_Q_Email_Controller::email_header() Output the email header
 */
do_action( 'addify_rfq_woocommerce_email_header', $email_heading, $email );
?>

<style type="text/css">
	#body_content_inner p{
		margin-top: 0!important;
	}
</style>
	<?php
		echo wp_kses_post(
			str_replace(
				array( '{user_name}', '{quote_id}' ),
				array( $user_name, $quote_id ),
				preg_replace('/<p(.*?)>/', '<p$1 style="margin-top: 0;margin-bottom: 8px;">', wpautop( wptexturize( apply_filters( 'addify_rfq_email_text', $email_message ) ) ))
			)
		);

/*
 * @hooked AF_R_F_Q_Email_Controller::customer_details() Shows customer details
 * @hooked AF_R_F_Q_Email_Controller::email_address() Shows email address
 */
do_action( 'addify_rfq_email_customer_details', $quote_id, $email );


do_action( 'addify_rfq_email_after_customer_details', $quote_id, $email );

/*
 * @hooked AF_R_F_Q_Email_Controller::order_details() Shows the order details table.
 *
 */

do_action( 'addify_rfq_email_quote_details', $quote_id, $email, 'af_email_to_admin' );

/*
 * @hooked AF_R_F_Q_Email_Controller::order_meta() Shows order meta data.
 */
do_action( 'addify_rfq_email_quote_meta', $quote_id, $email );

/*
 * @hooked AF_R_F_Q_Email_Controller::email_footer() Output the email footer
 */
do_action( 'addify_rfq_woocommerce_email_footer', $email );
