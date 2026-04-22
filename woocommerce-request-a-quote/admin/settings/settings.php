<?php
/**
 * Settings for cart restrictions
 *
 * @package  woocommerce-request-a-quote
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( isset( $_GET['tab'] ) ) {
	$active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
} else {
	$active_tab = 'general';
}

?>
<div class="af-rfq-settings">
	<ul class="subsubsub">
		<?php settings_errors(); ?>
		<li>
			<a href="?page=af-rfq-settings&tab=general" class=" <?php echo esc_attr( $active_tab ) === 'general' ? 'current' : ''; ?>"><?php echo esc_html__( 'General', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=convert_to_cart_restrict" class=" <?php echo esc_attr( $active_tab ) === 'convert_to_cart_restrict' ? 'current' : ''; ?>"><?php echo esc_html__( 'Convert to Cart Restriction', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=messages" class=" <?php echo esc_attr( $active_tab ) === 'messages' ? 'current' : ''; ?>"><?php echo esc_html__( 'Custom Messages', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=emails" class=" <?php echo esc_attr( $active_tab ) === 'emails' ? 'current' : ''; ?>"><?php echo esc_html__( 'Emails', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=captcha" class=" <?php echo esc_attr( $active_tab ) === 'captcha' ? 'current' : ''; ?>"><?php echo esc_html__( 'Google Captcha', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=editors" class=" <?php echo esc_attr( $active_tab ) === 'editors' ? 'current' : ''; ?>"><?php echo esc_html__( 'Page builders', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=attributes" class=" <?php echo esc_attr( $active_tab ) === 'attributes' ? 'current' : ''; ?>"><?php echo esc_html__( 'Quote Attributes', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=button" class=" <?php echo esc_attr( $active_tab ) === 'button' ? 'current' : ''; ?>"><?php echo esc_html__( 'Quote Page Customization', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=popup_design" class=" <?php echo esc_attr( $active_tab ) === 'popup_design' ? 'current' : ''; ?>"><?php echo esc_html__( 'Quote Popup Customization', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=quote_btn_shortcode" class=" <?php echo esc_attr( $active_tab ) === 'quote_btn_shortcode' ? 'current' : ''; ?>"><?php echo esc_html__( 'Quote Button Shortcode', 'addify_b2b' ); ?>
			</a>|
		</li>
		<li>
			<a href="?page=af-rfq-settings&tab=pdf_layout" class=" <?php echo esc_attr( $active_tab ) === 'pdf_layout' ? 'current' : ''; ?>"><?php echo esc_html__( 'PDF Settings', 'addify_b2b' ); ?>
			</a>
		</li>
	</ul>
	<br class="clear">
	<form method="post" action="options.php" class="afrfq_options_form">
		<?php

		if ( 'general' === $active_tab ) {
			settings_fields( 'afrfq_general_setting_fields' );
			do_settings_sections( 'afrfq_general_setting_section' );
		} elseif ( 'convert_to_cart_restrict' === $active_tab ) {
			settings_fields( 'afrfq_convert_to_cart_restrict_fields' );
			do_settings_sections( 'afrfq_convert_to_cart_restrict_section' );
		} elseif ( 'messages' === $active_tab ) {
			settings_fields( 'afrfq_messages_fields' );
			do_settings_sections( 'afrfq_messages_section' );
		} elseif ( 'emails' === $active_tab ) {
			settings_fields( 'afrfq_emails_fields' );
			do_settings_sections( 'afrfq_emails_section' );
		} elseif ( 'captcha' === $active_tab ) {
			settings_fields( 'afrfq_captcha_fields' );
			do_settings_sections( 'afrfq_captcha_section' );
		} elseif ( 'editors' === $active_tab ) {
			settings_fields( 'afrfq_editors_fields' );
			do_settings_sections( 'afrfq_editors_section' );
		} elseif ( 'attributes' === $active_tab ) {
			settings_fields( 'afrfq_attributes_fields' );
			do_settings_sections( 'afrfq_attributes_section' );
		} elseif ( 'button' === $active_tab ) {
			settings_fields( 'afrfq_button_setting_fields' );
			do_settings_sections( 'afrfq_button_setting_section' );
		} elseif ( 'popup_design' === $active_tab ) {
			settings_fields( 'afrfq_popup_setting_fields' );
			do_settings_sections( 'afrfq_popup_design_setting_section' );
		} elseif ( 'quote_btn_shortcode' === $active_tab ) {
			settings_fields( 'afrfq_quote_btn_shortcode_fields' );
			do_settings_sections( 'afrfq_quote_btn_shortcode_setting_section' );
		} elseif ( 'pdf_layout' === $active_tab ) {
			settings_fields( 'afrfq_pdflayout_setting_fields' );
			do_settings_sections( 'afrfq_pdflayout_setting_section' );
		}

		if ('quote_btn_shortcode' != $active_tab) {
			submit_button();
		}
		?>
	</form>
</div>
