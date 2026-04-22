<?php
/**
 * Quote Button Shortcode settings tab fields
 *
 * @package  woocommerce-request-a-quote
 * @version  2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-quote-btn-shortcode-page',         
	esc_html__( 'Quote Button Shortcode', 'addify_b2b' ),   
	'afrfq_quote_btn_shortcode_setting_section_callback',
	'afrfq_quote_btn_shortcode_setting_section'                           
);

add_settings_field(
	'afrfq_add_to_quote_shortcode',                      
	esc_html__( 'Shortcode', 'addify_b2b' ),    
	'afrfq_add_to_quote_shortcode_callback',   
	'afrfq_quote_btn_shortcode_setting_section',                          
	'afrfq-quote-btn-shortcode-page',         
	array( esc_html__( 'Select quote popup layout.', 'addify_b2b' ) )
);
register_setting(
	'afrfq_quote_btn_shortcode_fields',
	'afrfq_add_to_quote_shortcode'
);


if ( ! function_exists( 'afrfq_quote_btn_shortcode_setting_section_callback' ) ) {
	/**
	 *  AF_R_F_Q_Admin.
	 *
	 * @param array $args returns array.
	 */
	function afrfq_quote_btn_shortcode_setting_section_callback( $args = array() ) {
		?>
		<p class="description"> <?php echo esc_html__("You can add the 'Add to Quote' button anywhere on your website using the shortcode.", 'addify_b2b'); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_add_to_quote_shortcode_callback' ) ) {
	/**
	 *  AF_R_F_Q_Admin.
	 *
	 * @param array $args returns array.
	 */
	function afrfq_add_to_quote_shortcode_callback( $args = array() ) {
		?>
		<div class="afrfq-shortcode-container">
			<code class="afrfq-shortcode-text" style="display: block;padding: 20px 10px;">
				[AFRFQ_QUOTE_BUTTON text='Add to Quote' product-id='408']
			</code>
			<button class="afrfq-copy-shortcode" style="position: absolute; top: 8px; right: 8px; padding: 6px 12px; font-size: 12px; border: none; background-color: #007bff; color: #fff; border-radius: 4px; cursor: pointer;">
			   <?php echo esc_html__('Copy', 'addify_b2b'); ?>
			</button>
		</div>
		<p class="description shortcode-desc"><?php echo esc_html__('Creates a simple "Add to Quote" button for a specific product. Requires the product ID and allows customization of the button text. For variable products, provide the variation ID. The product (or variation) will be added directly to the quote list when clicked.', 'addify_b2b'); ?></p>

		<div class="afrfq-shortcode-container">
			<code class="afrfq-shortcode-text" style="display: block;padding: 20px 10px;">
				[AFRFQ_QUOTE_BUTTON text='Add to Quote' class='your_css_class' product-id='408']
			</code>
			<button class="afrfq-copy-shortcode" style="position: absolute; top: 8px; right: 8px; padding: 6px 12px; font-size: 12px; border: none; background-color: #007bff; color: #fff; border-radius: 4px; cursor: pointer;">
			   <?php echo esc_html__('Copy', 'addify_b2b'); ?>
			</button>
		</div>
		<p class="description shortcode-desc"><?php echo esc_html__('Generates a customizable quote button with optional CSS classes for styling. Includes all basic functionality while allowing design customization through additional CSS classes.', 'addify_b2b'); ?></p>

		<div class="afrfq-shortcode-container">
			<code class="afrfq-shortcode-text" style="display: block;padding: 20px 10px;">
				[AFRFQ_QUOTE_BUTTON text='Add to Quote' product-id='408' product-quantity='2']
			</code>
			<button class="afrfq-copy-shortcode" style="position: absolute; top: 8px; right: 8px; padding: 6px 12px; font-size: 12px; border: none; background-color: #007bff; color: #fff; border-radius: 4px; cursor: pointer;">
			   <?php echo esc_html__('Copy', 'addify_b2b'); ?>
			</button>
		</div>
		<p class="description shortcode-desc"><?php echo esc_html__('Creates a quote button that adds a specified quantity of the product. The product-quantity attribute determines how many units will be included in the quote request when clicked.', 'addify_b2b'); ?></p>

		
		<div class="afrfq-shortcode-container">
			<code class="afrfq-shortcode-text" style="display: block;padding: 20px 10px;">
				[AFRFQ_QUOTE_BUTTON text='Add to Quote' class='your_css_class' product-id='408' popup-enabled='yes']
			</code>
			<button class="afrfq-copy-shortcode" style="position: absolute; top: 8px; right: 8px; padding: 6px 12px; font-size: 12px; border: none; background-color: #007bff; color: #fff; border-radius: 4px; cursor: pointer;">
			   <?php echo esc_html__('Copy', 'addify_b2b'); ?>
			</button>
		</div>
		<p class="description shortcode-desc"><?php echo esc_html__('Creates an enhanced quote button with both styling options and add to quote via popup. When enabled, clicking the button displays a popup dialog that will handle place quote functionality.', 'addify_b2b'); ?></p>
	   
	   
		<?php
	}
}

