<?php
/**
 * Quote attributes tab fields
 *
 * @package  woocommerce-request-a-quote
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-attributes-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Quote Attributes Settings', 'addify_b2b' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_attributes_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_enable_for_specific_user_role',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable For Specific User Role', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_for_specific_user_role_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_attributes_section',                          // The page on which this option will be displayed.
	'afrfq-attributes-sec',         // The name of the section to which this field belongs.
	array( 
		esc_html__( 'The options for product price, offered price, and tax display below will be visible only for the selected user roles. Leave empty to apply to all user roles.', 'addify_b2b' ), 
		esc_html__( 'Note: For user roles that are not selected, the product price, offered price, and tax display will be hidden.', 'addify_b2b' ),
	)
	
);

register_setting(
	'afrfq_attributes_fields',
	'afrfq_enable_for_specific_user_role'
);

add_settings_field(
	'afrfq_enable_pro_price',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Product Price', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_pro_price_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_attributes_section',                          // The page on which this option will be displayed.
	'afrfq-attributes-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable product price, subtotal and total of quote basket.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_attributes_fields',
	'afrfq_enable_pro_price'
);

add_settings_field(
	'afrfq_enable_off_price',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Offered Price', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_off_price_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_attributes_section',                          // The page on which this option will be displayed.
	'afrfq-attributes-sec',         // The name of the section to which this field belongs.
	array(
		esc_html__( 'Enable offered price and subtotal(offered price) of quote basket.', 'addify_b2b' ),
		esc_html__( 'Note: offered price will be excluding tax if products prices are excluding tax and including tax if prices are including tax.', 'addify_b2b' ),
	)
);

register_setting(
	'afrfq_attributes_fields',
	'afrfq_enable_off_price'
);

add_settings_field(
	'afrfq_enable_off_price_increase',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Increase Offered Price', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_off_price_increase_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_attributes_section',                          // The page on which this option will be displayed.
	'afrfq-attributes-sec',         // The name of the section to which this field belongs.
	array(
		esc_html__( 'Enter number in percent to increase the offered price from standard price of product. Leave empty for standard price.', 'addify_b2b' ),
		esc_html__( 'Note: offered price will be display according to settings of cart. (including/excluding tax)', 'addify_b2b' ),
	)
);

register_setting(
	'afrfq_attributes_fields',
	'afrfq_enable_off_price_increase'
);

add_settings_field(
	'afrfq_enable_tax',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Tax Display', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_tax_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_attributes_section',                          // The page on which this option will be displayed.
	'afrfq-attributes-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable tax calculation of quote basket items.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_attributes_fields',
	'afrfq_enable_tax'
);

add_settings_field(
	'afrfq_enable_convert_order',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Convert to Order', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_convert_order_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_attributes_section',                          // The page on which this option will be displayed.
	'afrfq-attributes-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable convert to order for customers(Quote Status: In Process, Accepted, Converted to Cart).', 'addify_b2b' ) )
);

register_setting(
	'afrfq_attributes_fields',
	'afrfq_enable_convert_order'
);

add_settings_field(
	'afrfq_enable_converted_by',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Quote Converter Display', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_converted_by_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_attributes_section',                          // The page on which this option will be displayed.
	'afrfq-attributes-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable display of quote converted (User/Admin) in my-account quote details.', 'addify_b2b' ) )
);

register_setting(
	'afrfq_attributes_fields',
	'afrfq_enable_converted_by'
);

add_settings_field(
	'afrfq_enable_convert_to_cart',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Convert to Cart', 'addify_b2b' ),    // The label to the left of the option interface element.
	'afrfq_enable_convert_to_cart_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_attributes_section',                          // The page on which this option will be displayed.
	'afrfq-attributes-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable convert to cart for customers(Quote Status: In Process, Accepted).', 'addify_b2b' ) )
);

register_setting(
	'afrfq_attributes_fields',
	'afrfq_enable_convert_to_cart'
);



if ( ! function_exists( 'afrfq_enable_convert_to_cart_callback' ) ) {
	/**
	 *  AF_R_F_Q_Admin.
	 *
	 * @param array $args returns array.
	 */
	function afrfq_enable_convert_to_cart_callback( $args = array() ) {
		?>
	<input type="checkbox" name="afrfq_enable_convert_to_cart" id="afrfq_enable_convert_to_cart" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_convert_to_cart' ) ) ); ?> />
	<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
	<?php
	}
}

if ( ! function_exists( 'afrfq_enable_converted_by_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_converted_by_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_converted_by" id="afrfq_enable_converted_by" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_converted_by' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_convert_order_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_convert_order_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_convert_order" id="afrfq_enable_convert_order" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_convert_order' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_tax_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_tax_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_tax" id="afrfq_enable_tax" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_tax' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_off_price_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_off_price_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_off_price" id="afrfq_enable_off_price" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_off_price' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[1] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_off_price_increase_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_off_price_increase_callback( $args = array() ) {
		?>
		<input type="number" name="afrfq_enable_off_price_increase" id="afrfq_enable_off_price_increase" min="0" value="<?php echo esc_attr( get_option( 'afrfq_enable_off_price_increase' ) ); ?>" style="min-width: 300px;" />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[1] ); ?> </p>
		<?php
	}
}

if (!function_exists('afrfq_enable_for_specific_user_role_callback')) {
	function afrfq_enable_for_specific_user_role_callback( $args = array() ) {
		$afrfq_enable_for_specific_user_role = (array) get_option('afrfq_enable_for_specific_user_role');
		global $wp_roles;
		$roles = $wp_roles->get_names();
		?>
		<select class="afrfq_enable_for_specific_user_role" name="afrfq_enable_for_specific_user_role[]" data-placeholder="<?php echo esc_html__('Select Roles', 'addify_b2b'); ?>" multiple="multiple" style="min-width: 300px;">
		<?php
		foreach ( $roles as $key => $value ) {
			?>
			<option value="<?php echo esc_attr( $key ); ?>" 
										<?php
										if ( ! empty( $afrfq_enable_for_specific_user_role ) && in_array( (string) $key, $afrfq_enable_for_specific_user_role, true ) ) {
							echo 'selected'; }
										?>
			><?php echo esc_attr( $value ); ?>
			</option>

		<?php } ?>
			<option value="guest" 
			<?php
			if ( ! empty( $afrfq_enable_for_specific_user_role ) && in_array( 'guest', $afrfq_enable_for_specific_user_role, true ) ) {
				echo 'selected'; }
			?>
			><?php echo esc_html__( 'Guest', 'addify_b2b' ); ?></option>
		</select>

		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[1] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_pro_price_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_pro_price_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_pro_price" id="afrfq_enable_pro_price" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_pro_price' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}
