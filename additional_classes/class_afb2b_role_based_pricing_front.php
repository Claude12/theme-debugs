<?php

if (! defined('ABSPATH') ) {
	exit; // restict for direct access
}

if (! class_exists('Front_Class_Addify_Customer_And_Role_Pricing') ) {

	class Front_Class_Addify_Customer_And_Role_Pricing extends Addify_B2B_Plugin {
	

		private $allfetchedrules;

		public function __construct() {

			$this->allfetchedrules = $this->csp_load();

			add_action('wp_loaded', array( $this, 'csp_front_scripts' ));

			//pricing templates design css
			if ('yes' === get_option('csp_enable_tire_price_table')) {
				add_action('wp_head', array( $this, 'csp_load_pricing_templates_styles' ));
			}
			
			// Change Price HTML
			add_filter('woocommerce_get_price_html', array( $this, 'af_csp_custom_price_html' ), 90, 2);

			add_filter('woocommerce_product_variation_get_price', array( $this, 'af_csp_custom_price_range' ), 99, 2);
			add_filter('woocommerce_product_variation_get_sale_price', array( $this, 'af_csp_custom_price_range' ), 99, 2);

			$enfore_min_max_qty = get_option('csp_enfore_min_max_qty');

			if (!empty($enfore_min_max_qty) && 'yes' == $enfore_min_max_qty) {
				// Min and Max Qty validation
				add_filter('woocommerce_add_to_cart_validation', array( $this, 'csp_validate_min_max_qty' ), 10, 4);

				// Update Cart validation
				add_filter('woocommerce_update_cart_validation', array( $this, 'csp_update_cart_quantity_validation' ), 10, 4);
			}

			add_action('woocommerce_before_add_to_cart_form', array( $this, 'af_csp_show_discount' ), 20);

			add_action('woocommerce_before_calculate_totals', array( $this, 'af_csp_recalculate_price' ), 20, 1);

			add_filter('woocommerce_cart_item_price', array( $this, 'af_csp_woocommerce_cart_item_price_filter' ), 10, 3);

			add_filter('woocommerce_available_variation', array( $this, 'af_csp_custom_variation_price_text' ), 10, 3);
		}

		public function csp_load() {

			// get Rules
			$args = array(
				'post_type'        => 'csp_rules',
				'post_status'      => 'publish',
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'numberposts'      => -1,
				'suppress_filters' => false,
			);

			return get_posts( $args );
		}


		public function csp_front_scripts() {

			wp_enqueue_style('addify_csp_front_css', plugins_url('../assets/css/addify_csp_front_css.css', __FILE__), false, '1.0');
			wp_enqueue_script('af_csp_front_js', plugins_url('../assets/js/addify_csp_front_js.js', __FILE__), array( 'jquery' ), '1.0');

			$afb2b_role_php_vars = array(
				'admin_url' => admin_url('admin-ajax.php'),
				'nonce'     => wp_create_nonce('afb2b-role-ajax-nonce'),
			);

			wp_localize_script('af_csp_front_js', 'afb2b_role_php_vars', $afb2b_role_php_vars);
		}

		public function csp_load_pricing_templates_styles() {
			$afb2b_role_enable_table_border = get_option('afb2b_role_enable_table_border');
			$afb2b_role_table_border_color  = get_option('afb2b_role_table_border_color');
			
			$this->csp_template_styles();
	
			if ('yes' == $afb2b_role_enable_table_border) {
				$this->csp_role_table_border($afb2b_role_table_border_color);
			}
		}

		public function csp_template_styles() {

			$afb2b_role_default_template_text_color = !empty(get_option('afb2b_role_default_template_text_color'))?get_option('afb2b_role_default_template_text_color'):'#6D6D6D';
			$afb2b_role_default_template_font_size  = !empty(get_option('afb2b_role_default_template_font_size'))?get_option('afb2b_role_default_template_font_size'):'20';
			?>
			<style>
				.dynamic_price_display{
					display:none;
				}
				.afb2b_role_list_div,.afb2b_role_table_div,.afb2b_role_card_div,.afb2b_role_template_div{
					font-family: <?php echo esc_attr(get_option('afb2b_role_template_font_family')); ?>
				}

				.afb2b_role_default_div{
					color: <?php echo esc_attr($afb2b_role_default_template_text_color); ?>
				}

				.afb2b_role_default_div{
					font-size: <?php echo esc_attr($afb2b_role_default_template_font_size) . 'px'; ?>
				}


				.afb2b_role_template_div .afb2b_role_template_header h2{
					font-size: <?php echo esc_attr(get_option('afb2b_role_template_heading_text_font_size')); ?>px;
				}

				table:not( .has-background ) tbody td {
					background-color: initial;
				}

				table.afb2b_role_table tbody tr:nth-child(odd) {
					background-color: <?php echo esc_attr(get_option('afb2b_role_table_odd_rows_color')); ?>;
				}

				table.afb2b_role_table tbody tr:nth-child(odd) {
					color: <?php echo esc_attr(get_option('afb2b_role_table_odd_rows_text_color')); ?>;
				}

				table:not( .has-background ) tbody tr:nth-child(2n) td {
					background-color: initial;
				}

				table.afb2b_role_table tbody tr:nth-child(even) {
					background-color: <?php echo esc_attr(get_option('afb2b_role_table_even_rows_color')); ?>;
				}

				table.afb2b_role_table tbody tr:nth-child(even) {
					color: <?php echo esc_attr(get_option('afb2b_role_table_even_rows_text_color')); ?>;
				}

				table.afb2b_role_table tbody tr {
					font-size: <?php echo esc_attr(get_option('afb2b_role_table_rows_font_size')); ?>px;
				}

				.afb2b_role_list_box{
					border: 1px solid <?php echo esc_attr(get_option('afb2b_role_list_border_color')); ?>;
				}

				.afb2b_role_list_box{
					background-color: <?php echo esc_attr(get_option('afb2b_role_list_background_color')); ?>;
				}

				.afb2b_role_list_box{
					color: <?php echo esc_attr(get_option('afb2b_role_list_text_color')); ?>;
				}

				.afb2b_role_selected_list{
					background-color: <?php echo esc_attr(get_option('afb2b_role_selected_list_background_color')); ?>;
				}

				.afb2b_role_selected_list{
					color: <?php echo esc_attr(get_option('afb2b_role_selected_list_text_color')); ?>;
				}

				.afb2b_role_inner_small_box{
					border: 1px solid <?php echo esc_attr(get_option('afb2b_role_card_border_color')); ?>;
				}

				.afb2b_role_inner_small_box{
					color:<?php echo esc_attr(get_option('afb2b_role_card_text_color')); ?>;
				}

				.afb2b_role_inner_small_box{
					background-color: <?php echo esc_attr(get_option('afb2b_role_card_background_color')); ?>;
				}

				.afb2b_role_selected_card{
					border: 2px solid <?php echo esc_attr(get_option('afb2b_role_selected_card_border_color')); ?>;
				}

				.afb2b_role_sale_tag{
					background-color: <?php echo esc_attr(get_option('afb2b_role_sale_tag_background_color')); ?>;
				}

				.afb2b_role_sale_tag{
					color: <?php echo esc_attr(get_option('afb2b_role_sale_tag_text_color')); ?>;
				}

			</style>
		<?php
		}
		

		public function csp_role_table_border( $border_color ) {

			?>
			<style>
				.afb2b_role_table_div table {
					border-collapse: collapse;
					border: 2px solid <?php echo esc_attr($border_color); ?>;
				}
				.afb2b_role_table_div table.afb2b_role_table th, table.afb2b_role_table td {
					border: 1px solid <?php echo esc_attr($border_color); ?>;
					text-align:center
				}
			</style>
		<?php
		}

		public function af_csp_custom_price_html( $price, $product ) {

			$prices             = $price;
			$user               = wp_get_current_user();
			$role               = ( array ) $user->roles;
			$price_for_discount = get_option('afb2b_discount_price');
			$current_user       = wp_get_current_user();
			$first_role         = current($current_user->roles);

			$active_currency = get_woocommerce_currency();
			$base_currency   = get_option( 'woocommerce_currency' );

			if ( 'variable' ==  $product->get_type() ) {

				$variations              = $product->get_children();
				$product_variation_level = false;
				
				foreach ($variations as $variation_id) {
					$product_variation = wc_get_product($variation_id);
					if ( is_user_logged_in() ) {

						$user = wp_get_current_user();

						$cus_base_price  = get_post_meta($product_variation->get_id(), '_cus_base_price', true);
						$role_base_price = get_post_meta($product_variation->get_id(), '_role_base_price', true);

						if (empty($cus_base_price)) {
							$cus_base_price = array();
						}

						foreach ( $cus_base_price as $rule_cus_price) {

							if ( !empty($rule_cus_price['customer_name']) && $user->ID == $rule_cus_price['customer_name']) {
								$product_variation_level = true;
								break;
							}
						}
						
						if ( $product_variation_level ) {
							break;
						}

						//get role base price
						if (empty($role_base_price)) {
							$role_base_price = array();
						}

						foreach ( $role_base_price as $role_cus_price) {

							if ( !empty($role_cus_price['user_role']) && current( $role ) == $role_cus_price['user_role']) {
								$product_variation_level = true;
								break;
							}
						}

						if ( $product_variation_level ) {
							break;
						}
						
					} elseif ( !is_user_logged_in() ) {

							$role_base_price = get_post_meta($product_variation->get_id(), '_role_base_price', true);

							//get role base price
						if (empty($role_base_price)) {
							$role_base_price = array();
						}

						foreach ( $role_base_price as $role_cus_price) {

							if ( !empty($role_cus_price['user_role']) && 'guest' == $role_cus_price['user_role']) {
								$product_variation_level = true;
									
							}
						}

					}

				}//end foreach

				
				$min_price =  999999999999999999999999999999;
				$max_price = 0; 
				if ( $product_variation_level ) {
					$variations = $product->get_children();                 
					foreach ($variations as $variation_id) {
						$variation = wc_get_product( $variation_id );


						if (is_user_logged_in() ) {

							

							
							if (!empty($price_for_discount[ $first_role ]) && 'sale' == $price_for_discount[ $first_role ] && !empty($variation->get_sale_price())) {

								$price = $variation->get_sale_price();

							} elseif (!empty($price_for_discount[ $first_role ]) && 'regular' == $price_for_discount[ $first_role ] && !empty($variation->get_regular_price())) {

								$price = $variation->get_price();

							} else {

								$price = $variation->get_price();
							}


						} elseif (!empty($price_for_discount['guest']) && 'sale' == $price_for_discount['guest'] && !empty($variation->get_sale_price())) {

							

								$price = $variation->get_sale_price();

						} elseif (!empty($price_for_discount['guest']) && 'regular' == $price_for_discount['guest'] && !empty($variation->get_regular_price())) {

							$price = $variation->get_regular_price();

						} else {

							$price = $variation->get_price();

						}


						//$price     = $variation->get_price();
						if ( $price > $max_price ) {
							$max_price = $price ;
						}
						if ( $price < $min_price ) {
							$min_price = $price ;
						}
					}

					$min_price = wc_get_price_to_display( $product, array(
						'qty'   => 1,
						'price' => $min_price,
					) );
					$max_price = wc_get_price_to_display( $product, array(
						'qty'   => 1,
						'price' => $max_price,
					) );

					//Aelia currency switcher compatibility
					$converted_amount_min_price = apply_filters('wc_aelia_cs_convert', $min_price, $base_currency, $active_currency);
					$converted_amount_max_price = apply_filters('wc_aelia_cs_convert', $max_price, $base_currency, $active_currency);
					

					if ($min_price == $max_price) {

						

						$prices = '<ins class="highlight">' . wc_price( $converted_amount_min_price ) . '</ins>';

						$price_suffix = $product->get_price_suffix($converted_amount_min_price );

						if ( ! empty( $price_suffix ) ) {

							$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

						}


					} else {

						$prices = '<ins class="highlight">' . wc_price( $converted_amount_min_price ) . ' - ' . wc_price( $converted_amount_max_price ) . '</ins>';
					}

					if (is_product() &&  'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
						return $price;
					} else {
						return $prices;
					}
				}

			}


			if (is_user_logged_in() ) {



				if (!empty($price_for_discount[ $first_role ]) && 'regular' == $price_for_discount[ $first_role ] && !empty(get_post_meta($product->get_id(), '_regular_price', true))) {
					$pro_price = (float) get_post_meta($product->get_id(), '_regular_price', true);

				} elseif (!empty($price_for_discount[ $first_role ]) && 'sale' == $price_for_discount[ $first_role ] && !empty(get_post_meta($product->get_id(), '_sale_price', true))) {
					$pro_price = (float) get_post_meta($product->get_id(), '_sale_price', true);

				} else {
					$pro_price = (float) get_post_meta($product->get_id(), '_price', true);


				}
			} elseif (!is_user_logged_in() ) {

				if (!empty($price_for_discount['guest']) && 'regular' == $price_for_discount['guest'] && get_post_meta($product->get_id(), '_regular_price', true)) {
					$pro_price = (float) get_post_meta($product->get_id(), '_regular_price', true);
				} elseif (!empty($price_for_discount['guest']) && 'sale' == $price_for_discount['guest'] && get_post_meta($product->get_id(), '_sale_price', true)) {
					$pro_price = (float) get_post_meta($product->get_id(), '_sale_price', true);
				} else {

					$pro_price = (float) get_post_meta($product->get_id(), '_price', true);
				}
			}

			

			

			if ( 'incl' == $this->get_tax_price_display_mode() ) {
				
				$pro_price_to_display1 = wc_get_price_including_tax( $product, array(
					'qty'   => 1,
					'price' => $pro_price,
				) );
			} else {
				
				$pro_price_to_display1 = wc_get_price_excluding_tax( $product, array(
					'qty'   => 1,
					'price' => $pro_price,
				) );
			}

			//Aelia currency switcher compatibility
			$pro_price_to_display = apply_filters('wc_aelia_cs_convert', $pro_price_to_display1, $base_currency, $active_currency);


			if (is_user_logged_in() ) {

				// get customer specifc price
				$cus_base_price = get_post_meta($product->get_id(), '_cus_base_price', true);

				// get role base price
				$role_base_price = get_post_meta($product->get_id(), '_role_base_price', true);                    

				if (! empty($cus_base_price) ) {

					foreach ( $cus_base_price as $cus_price ) {

						if (isset($cus_price['customer_name']) && $user->ID == $cus_price['customer_name'] ) {

							if (( '' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) && 1 >= $cus_price['min_qty']) {

								if ('fixed_price' == $cus_price['discount_type'] ) {

									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $cus_price['discount_value'],
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $cus_price['discount_value'],
										) );
									}

									//Aelia currency switcher compatibility
									$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


									if (! empty($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price'] ) {

										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										
									} elseif ($pro_price_to_display > $converted_amount ) {
										
											$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
									} else {
										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

									}

									$price_suffix = $product->get_price_suffix($converted_amount );

									if ( ! empty( $price_suffix ) ) {

										$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

									}

									if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
										return $price;
									} else {
										return $prices;
									}

								} elseif ('fixed_increase' == $cus_price['discount_type'] ) {

									$newprice_act = $pro_price + $cus_price['discount_value'];
									//Aelia currency switcher compatibility
									$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice_act,
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice_act,
										) );
									}

									//Aelia currency switcher compatibility
									$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									$prices       = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
									$price_suffix = $product->get_price_suffix($converted_amount_act );

									if ( ! empty( $price_suffix ) ) {

										$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

									}

									if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
										return $price;
									} else {
										return $prices;
									}

								} elseif ('fixed_decrease' == $cus_price['discount_type'] ) {

									$newprice_act = $pro_price - $cus_price['discount_value'];
									//Aelia currency switcher compatibility
									$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
									
									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice_act,
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice_act,
										) );
									}

									//Aelia currency switcher compatibility
									$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									if (! empty($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price'] ) {

										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

									} elseif ($pro_price_to_display > $converted_amount ) {

											$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
									} else {
										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
									}

									$price_suffix = $product->get_price_suffix($converted_amount_act );

									if ( ! empty( $price_suffix ) ) {

										$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

									}

									if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
										return $price;
									} else {
										return $prices;
									}

								} elseif ('percentage_decrease' == $cus_price['discount_type'] ) {

									$percent_price = $pro_price * $cus_price['discount_value'] / 100;
									$newprice_act  = $pro_price - $percent_price;

									//Aelia currency switcher compatibility
									$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
									
									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice_act,
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice_act,
										) );
									}

									//Aelia currency switcher compatibility
									$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									if (! empty($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price'] ) {

										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

									} elseif ($pro_price_to_display > $converted_amount ) {
											$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
									} else {
										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
									}

									$price_suffix = $product->get_price_suffix($converted_amount_act );

									if ( ! empty( $price_suffix ) ) {

										$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

									}

									if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
										return $price;
									} else {
										return $prices;
									}

								} elseif ('percentage_increase' == $cus_price['discount_type'] ) {

									$percent_price = $pro_price * $cus_price['discount_value'] / 100;
									$newprice_act  = $pro_price + $percent_price;

									//Aelia currency switcher compatibility
									$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
									
									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice_act,
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice_act,
										) );
									}

									//Aelia currency switcher compatibility
									$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

									$price_suffix = $product->get_price_suffix($converted_amount_act );

									if ( ! empty( $price_suffix ) ) {

										$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

									}

									if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
										return $price;
									} else {
										return $prices;
									}

								} else {

									if ($pro_price_to_display > $cus_price['discount_value'] ) {
										$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($cus_price['discount_value']) . '</ins>';
									} else {
										$prices = '<ins class="highlight">' . wc_price($cus_price['discount_value']) . '</ins>';
									}


									$price_suffix = $product->get_price_suffix($pro_price );

									if ( ! empty( $price_suffix ) ) {

										$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

									}

								}
							} else {

								$prices = $price;
							}
						}
					}
				} else {

					$prices = $price;
				}

				// Role Based Pricing
				// chcek if there is customer specific pricing then role base pricing will not work.
				if (true ) {

					if (! empty($role_base_price) ) {

						foreach ( $role_base_price as $role_price ) {

							if (isset($role_price['user_role']) && current( $role ) == $role_price['user_role'] ) {

								if (( '' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) && 1 >= $role_price['min_qty']) {

									if ('fixed_price' == $role_price['discount_type'] ) {

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $role_price['discount_value'],
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $role_price['discount_value'],
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										if (! empty($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price'] ) {

											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										} elseif ($pro_price_to_display > $converted_amount ) {
												$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										} else {
											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										}

										$price_suffix = $product->get_price_suffix($converted_amount );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}

									} elseif ('fixed_increase' == $role_price['discount_type'] ) {

										$newprice_act = $pro_price + $role_price['discount_value'];
										//Aelia currency switcher compatibility
										$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										$price_suffix = $product->get_price_suffix($converted_amount_act );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}

									} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

										$newprice_act = $pro_price - $role_price['discount_value'];
										//Aelia currency switcher compatibility
										$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										if (! empty($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price'] ) {

											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										} elseif ($pro_price_to_display > $converted_amount ) {
												$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										} else {
											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										}

										$price_suffix = $product->get_price_suffix($converted_amount_act );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}

									} elseif ('percentage_decrease' == $role_price['discount_type'] ) {

										$percent_price = $pro_price * $role_price['discount_value'] / 100;
										$newprice_act  = $pro_price - $percent_price;

										//Aelia currency switcher compatibility
										$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										if (! empty($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price'] ) {

											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										} elseif ($pro_price_to_display > $converted_amount ) {
												$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										} else {
											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										}

										$price_suffix = $product->get_price_suffix($converted_amount_act );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}

									} elseif ('percentage_increase' == $role_price['discount_type'] ) {

										$percent_price = $pro_price * $role_price['discount_value'] / 100;
										$newprice_act  = $pro_price + $percent_price;

										//Aelia currency switcher compatibility
										$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										$price_suffix = $product->get_price_suffix($converted_amount_act );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}



									} else {

										if ($pro_price_to_display > $role_price['discount_value'] ) {
											$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($role_price['discount_value']) . '</ins>';
										} else {
											$prices = '<ins class="highlight">' . wc_price($role_price['discount_value']) . '</ins>';
										}


										$price_suffix = $product->get_price_suffix($pro_price );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

									}
								} else {
									$prices = $price;
								}
							}
						}
					} else {

						$prices = $price;
					}
				}

				// Rules
				if (true ) {

					if (empty($this->allfetchedrules) ) {

						echo '';

					} else {

						$all_rules = $this->allfetchedrules;

					}

					if (! empty($all_rules) ) {
						foreach ( $all_rules as $rule ) {  

							$istrue = false;

							$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
							$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
							$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
							$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );


							if ('yes' == $applied_on_all_products ) {
								$istrue = true;
							} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
								$istrue = true;
							}


							if (!empty($categories)) {
								foreach ( $categories as $cat ) {

									if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() )  ||  has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

										$istrue = true;
									} 
								}
							}

							if (!empty($rbp_slected_brands)) {
								foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

								

									if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product->get_id() ) ||  has_term( $rbp_brand_slect, 'product_brand', $product->get_parent_id() ) ) ) {

										$istrue = true;
									} 
								}
							}

							if ($istrue ) {
								if ($product->is_type('variable') ) {
									$min_price = $product->get_variation_price('min');
									$max_price = $product->get_variation_price('max');

									
								}

								// get Rule customer specifc price
								$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

								// get role base price
								$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

								$customer_discount = false;

								if (! empty($rule_cus_base_price) ) {
									foreach ( $rule_cus_base_price as $rule_cus_price ) {

										if ($user->ID == $rule_cus_price['customer_name'] ) {

											if (( '' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) && 1 >= $rule_cus_price['min_qty']) {

												if ('fixed_price' == $rule_cus_price['discount_type'] ) {

													if ($product->is_type('variable') ) {


														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_cus_price['discount_value'],
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_cus_price['discount_value'],
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


														if (! empty($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price'] ) {

															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														} elseif ($pro_price_to_display > $converted_amount ) {

																$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														} else {
															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														}


													} else {

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_cus_price['discount_value'],
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_cus_price['discount_value'],
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														if (! empty($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price'] ) {

															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														} elseif ($pro_price_to_display > $converted_amount ) {
																$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														} else {
															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														}
													}

													//Aelia currency switcher compatibility
													$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

													$price_suffix = $product->get_price_suffix($converted_amount );

													if ( ! empty( $price_suffix ) ) {

														$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

													}

													if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
														return $price;
													} else {
														return $prices;
													}
 
												} elseif ('fixed_increase' == $rule_cus_price['discount_type'] ) {

													if ($product->is_type('variable') ) {

														$newprice1 = $min_price + $rule_cus_price['discount_value'];
														$newprice2 = $max_price + $rule_cus_price['discount_value'];

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice1 = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice1,
															) );
														} else {
															$newprice1 = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice1,
															) );
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice2 = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice2,
															) );
														} else {
															$newprice2 = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice2,
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


														$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);

														
														
														if ($min_price == $max_price ) {

															$newprice_act = $pro_price + $rule_cus_price['discount_value'];

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															if (! empty($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} elseif ($pro_price_to_display > $converted_amount ) {

																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															}

															$price_suffix = $product->get_price_suffix($converted_amount_act );

															
														} else {

															$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
														}


													} else {

														$newprice_act = $pro_price + $rule_cus_price['discount_value'];
														//Aelia currency switcher compatibility
														$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
														
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice_act,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice_act,
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$prices       = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														$price_suffix = $product->get_price_suffix($converted_amount_act );
														
													}

													

													if ( ! empty( $price_suffix ) ) {

														$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

													}

													if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
														return $price;
													} else {
														return $prices;
													}

												} elseif ('fixed_decrease' == $rule_cus_price['discount_type'] ) {

													if ($product->is_type('variable') ) {

														$newprice1 = $min_price - $rule_cus_price['discount_value'];
														$newprice2 = $max_price - $rule_cus_price['discount_value'];

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice1 = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice1,
															) );
														} else {
															$newprice1 = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice1,
															) );
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice2 = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice2,
															) );
														} else {
															$newprice2 = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice2,
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


														$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);
														
														


														if ($min_price == $max_price ) {

															$newprice_act = $pro_price - $rule_cus_price['discount_value'];

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															
															if (! empty($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}

															$price_suffix = $product->get_price_suffix($converted_amount_act );

														} else {

															$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
														}
													} else {

														$newprice_act = $pro_price - $rule_cus_price['discount_value'];

														//Aelia currency switcher compatibility
														$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
														
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice_act,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice_act,
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														if (! empty($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price'] ) {

															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

														} elseif ($pro_price_to_display > $converted_amount ) {
																$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														} else {
															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														}

														$price_suffix = $product->get_price_suffix($converted_amount_act );
													}

													

													if ( ! empty( $price_suffix ) ) {

														$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

													}

													if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
														return $price;
													} else {
														return $prices;
													}

												} elseif ('percentage_decrease' == $rule_cus_price['discount_type'] ) {

													if ($product->is_type('variable') ) {

														$percent_price1 = $min_price * $rule_cus_price['discount_value'] / 100;
														$newprice1      = $min_price - $percent_price1;
														$percent_price2 = $max_price * $rule_cus_price['discount_value'] / 100;
														$newprice2      = $max_price - $percent_price2;

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice1 = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice1,
															) );
														} else {
															$newprice1 = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice1,
															) );
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice2 = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice2,
															) );
														} else {
															$newprice2 = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice2,
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


														$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);
														 
														

														if ($min_price == $max_price ) {

															$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;
															$newprice_act  = $pro_price - $percent_price;

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
															
															if (! empty($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}

															$price_suffix = $product->get_price_suffix($converted_amount_act );

														} else {

															$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
														}
													} else {

														$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;
														$newprice_act  = $pro_price - $percent_price;

														//Aelia currency switcher compatibility
														$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
														
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice_act,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice_act,
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														if (! empty($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price'] ) {

															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

														} elseif ($pro_price_to_display > $converted_amount ) {
																$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
														} else {
															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

														}

														$price_suffix = $product->get_price_suffix($converted_amount_act );
													}

													

													if ( ! empty( $price_suffix ) ) {

														$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

													}

													if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
														return $price;
													} else {
														return $prices;
													}

												} elseif ('percentage_increase' == $rule_cus_price['discount_type'] ) {

													if ($product->is_type('variable') ) {

														$percent_price1 = $min_price * $rule_cus_price['discount_value'] / 100;
														$newprice1      = $min_price + $percent_price1;
														$percent_price2 = $max_price * $rule_cus_price['discount_value'] / 100;
														$newprice2      = $max_price + $percent_price2;

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice1 = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice1,
															) );
														} else {
															$newprice1 = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice1,
															) );
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice2 = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice2,
															) );
														} else {
															$newprice2 = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice2,
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


														$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);


														 
														

														if ($min_price == $max_price ) {

															$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;
															$newprice_act  = $pro_price + $percent_price;

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
															
															if (! empty($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}

															$price_suffix = $product->get_price_suffix($converted_amount_act );

														} else {

															$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
														}
													} else {

														$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;
														$newprice_act  = $pro_price + $percent_price;

														//Aelia currency switcher compatibility
														$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
														
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice_act,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice_act,
															) );
														}

														//Aelia currency switcher compatibility
														$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

														$price_suffix = $product->get_price_suffix($converted_amount_act );
													}

													

													if ( ! empty( $price_suffix ) ) {

														$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

													}

													if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
														return $price;
													} else {
														return $prices;
													}

												} else {

													if ($pro_price_to_display > $rule_cus_price['discount_value'] ) {
														$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($rule_cus_price['discount_value']) . '</ins>';
													} else {
														$prices = '<ins class="highlight">' . wc_price($rule_cus_price['discount_value']) . '</ins>';
													}

													$price_suffix = $product->get_price_suffix($pro_price );

													if ( ! empty( $price_suffix ) ) {

														$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

													}
												}
											} else {

												$prices = $price;
											}
										}
									}
								} else {

									$prices = $price;
								}

								// Role Based Pricing
								// chcek if there is customer specific pricing then role base pricing will not work.
								if (true ) {

										
									if (! empty($rule_role_base_price) ) {
										foreach ( $rule_role_base_price as $rule_role_price ) {

											if (current( $role ) == $rule_role_price['user_role'] ) {

												if (( '' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) && 1 >= $rule_role_price['min_qty']) {

													if ('fixed_price' == $rule_role_price['discount_type'] ) {

														if ($product->is_type('variable') ) {

															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}



														} else {

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}
														}

														//Aelia currency switcher compatibility
														$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$price_suffix = $product->get_price_suffix($converted_amount );

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {

														if ($product->is_type('variable') ) {

	
															$newprice1 = $min_price + $rule_role_price['discount_value'];
															$newprice2 = $max_price + $rule_role_price['discount_value'];
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


															$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);



															if ($min_price == $max_price ) {

																$newprice_act = $pro_price + $rule_role_price['discount_value'];
																//Aelia currency switcher compatibility
																$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

																if ( 'incl' == $this->get_tax_price_display_mode() ) {
																	$newprice = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																} else {
																	$newprice = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																}

																//Aelia currency switcher compatibility
																$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} elseif ($pro_price_to_display > $converted_amount ) {
																		$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} else {
																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																}

																$price_suffix = $product->get_price_suffix($converted_amount_act );

															} else {

																$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
															}
														} else {

															$newprice_act = $pro_price + $rule_role_price['discount_value'];
															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															$price_suffix = $product->get_price_suffix($converted_amount_act );
														}

														

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

														if ($product->is_type('variable') ) {

															$newprice1 = $min_price - $rule_role_price['discount_value'];
															$newprice2 = $max_price - $rule_role_price['discount_value'];


															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


															$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);


															if ($min_price == $max_price ) {

																$newprice_act = $pro_price - $rule_role_price['discount_value'];

																//Aelia currency switcher compatibility
																$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

																
																if ( 'incl' == $this->get_tax_price_display_mode() ) {
																	$newprice = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																} else {
																	$newprice = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																}

																//Aelia currency switcher compatibility
																$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} elseif ($pro_price_to_display > $converted_amount ) {
																		$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} else {
																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																}

																$price_suffix = $product->get_price_suffix($converted_amount_act );



															} else {

																$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
															}
														} else {

															$newprice_act = $pro_price - $rule_role_price['discount_value'];
															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}

															$price_suffix = $product->get_price_suffix($converted_amount_act );
														}

														

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) { 

														if ($product->is_type('variable') ) { 

															$percent_price1 = $min_price * $rule_role_price['discount_value'] / 100;
															$newprice1      = $min_price - $percent_price1;

															$percent_price2 = $max_price * $rule_role_price['discount_value'] / 100;
															$newprice2      = $max_price - $percent_price2;

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


															$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);


															if ($min_price == $max_price ) { 

																
																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice_act = $pro_price - $percent_price;

																//Aelia currency switcher compatibility
																$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);


																if ( 'incl' == $this->get_tax_price_display_mode() ) {
																	$newprice = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																} else {
																	$newprice = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																}

																//Aelia currency switcher compatibility
																$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} elseif ($pro_price_to_display > $converted_amount ) {
																		$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} else {
																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																}

																$price_suffix = $product->get_price_suffix($converted_amount_act );

																

															} else {

																$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
															}
														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;
															$newprice_act  = $pro_price - $percent_price;

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}

															$price_suffix = $product->get_price_suffix($converted_amount_act );
														}

													

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {

														if ($product->is_type('variable') ) {


															$percent_price1 = $min_price * $rule_role_price['discount_value'] / 100;
															$newprice1      = $min_price + $percent_price1;

															$percent_price2 = $max_price * $rule_role_price['discount_value'] / 100;
															$newprice2      = $max_price + $percent_price2;

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


															$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);


															if ($min_price == $max_price ) {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice_act = $pro_price + $percent_price;

																//Aelia currency switcher compatibility
																$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

																
																if ( 'incl' == $this->get_tax_price_display_mode() ) {
																	$newprice = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																} else {
																	$newprice = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																}

																//Aelia currency switcher compatibility
																$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} elseif ($pro_price_to_display > $converted_amount ) {
																		$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} else {
																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																}

																$price_suffix = $product->get_price_suffix($converted_amount_act );


															} else {

																$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
															}
														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;
															$newprice_act  = $pro_price + $percent_price;

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															$price_suffix = $product->get_price_suffix($converted_amount_act );
														}

														

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} else {

														if ($pro_price_to_display > $rule_role_price['discount_value'] ) {
															$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($rule_role_price['discount_value']) . '</ins>';
														} else {
															$prices = '<ins class="highlight">' . wc_price($rule_role_price['discount_value']) . '</ins>';
														}

														$price_suffix = $product->get_price_suffix($pro_price );

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														// return $prices;
														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}
													}
												} else {

													$prices = $price;
													return $prices;
												}
											}
										}
									} else {

										$prices = $price;
										return $prices;
									}


								}
							}
						}
					}
				}
			} else {

				// Role Based Pricing for Non Loggedin
				// chcek if there is customer specific pricing then role base pricing will not work.
				if (true ) {

					// get role base price
					$role_base_price = get_post_meta($product->get_id(), '_role_base_price', true);
						
					if (! empty($role_base_price) ) {

						foreach ( $role_base_price as $role_price ) {

							if (isset($role_price['user_role']) && 'guest' == $role_price['user_role'] ) {

								if (( '' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) && 1 >= $role_price['min_qty']) {

									if ('fixed_price' == $role_price['discount_type'] ) {

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $role_price['discount_value'],
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $role_price['discount_value'],
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										if (! empty($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price'] ) {

											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										} elseif ($pro_price_to_display > $converted_amount ) {
												$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										} else {
											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										}

										$price_suffix = $product->get_price_suffix($converted_amount );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										// return $prices;
										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}


									} elseif ('fixed_increase' == $role_price['discount_type'] ) {

										$newprice_act = $pro_price + $role_price['discount_value'];
										//Aelia currency switcher compatibility
										$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										$price_suffix = $product->get_price_suffix($converted_amount_act );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}
										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}

									} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

										$newprice_act = $pro_price - $role_price['discount_value'];

										//Aelia currency switcher compatibility
										$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										if (! empty($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price'] ) {

											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										} elseif ($pro_price_to_display > $converted_amount ) {
												$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										} else {
											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										}

										$price_suffix = $product->get_price_suffix($converted_amount_act );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}

									} elseif ('percentage_decrease' == $role_price['discount_type'] ) {

										$percent_price = $pro_price * $role_price['discount_value'] / 100;
										$newprice_act  = $pro_price - $percent_price;

										//Aelia currency switcher compatibility
										$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										if (! empty($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price'] ) {

											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										} elseif ($pro_price_to_display > $converted_amount ) {
												$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
										} else {
											$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										}

										$price_suffix = $product->get_price_suffix($converted_amount_act );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}

									} elseif ('percentage_increase' == $role_price['discount_type'] ) {

										$percent_price = $pro_price * $role_price['discount_value'] / 100;
										$newprice_act  = $pro_price + $percent_price;

										//Aelia currency switcher compatibility
										$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice_act,
											) );
										}

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

										$price_suffix = $product->get_price_suffix($converted_amount_act );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

										if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
											return $price;
										} else {
											return $prices;
										}

									} else {

										if ($pro_price_to_display > $role_price['discount_value'] ) {
											$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($role_price['discount_value']) . '</ins>';
										} else {
											$prices = '<ins class="highlight">' . wc_price($role_price['discount_value']) . '</ins>';
										}


										$price_suffix = $product->get_price_suffix($pro_price );

										if ( ! empty( $price_suffix ) ) {

											$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

										}

									}
								} else {

									$prices = $price;
								}
							}
						}
					} else {

						$prices = $price;
					}

						
				} else {

					$prices = $price;
				}

				// Rules - guest users
				if (true ) {

					if (empty($this->allfetchedrules) ) {

						echo '';

					} else {

						$all_rules = $this->allfetchedrules;

					}

					if (! empty($all_rules) ) {
						foreach ( $all_rules as $rule ) {

							if ($product->is_type('variable') ) {
								$min_price = $product->get_variation_price('min');
								$max_price = $product->get_variation_price('max');

								
							}

							$istrue = false;
														

							$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
							$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
							$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
							$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

							if ('yes' == $applied_on_all_products ) {
								$istrue = true;
							} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
								$istrue = true;
							}


							if (!empty($categories)) {
								foreach ( $categories as $cat ) {

									if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() )  ||  has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

										$istrue = true;
									} 
								}
							}

							if (!empty($rbp_slected_brands)) {
								foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

									

									if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product->get_id() )  ||  has_term( $rbp_brand_slect, 'product_brand', $product->get_parent_id() ) ) ) {

										$istrue = true;
									} 
								}
							}


							if ($istrue ) {

								// get role base price
								$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

								// Role Based Pricing
								// chcek if there is customer specific pricing then role base pricing will not work.
								if (true ) {

									if (! empty($rule_role_base_price) ) {
										foreach ( $rule_role_base_price as $rule_role_price ) {

											if ('guest' == $rule_role_price['user_role'] ) {

												if (( '' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) && 1 >= $rule_role_price['min_qty']) {

													if ('fixed_price' == $rule_role_price['discount_type'] ) {

														if ($product->is_type('variable') ) {

															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


															if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}



														} else {

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}
														}

														$price_suffix = $product->get_price_suffix($converted_amount );

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {

														if ($product->is_type('variable') ) {

	
															$newprice1 = $min_price + $rule_role_price['discount_value'];
															$newprice2 = $max_price + $rule_role_price['discount_value'];
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


															$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);

															if ($min_price == $max_price ) {

																$newprice_act = $pro_price + $rule_role_price['discount_value'];

																//Aelia currency switcher compatibility
																$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

																if ( 'incl' == $this->get_tax_price_display_mode() ) {
																	$newprice = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																} else {
																	$newprice = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																}

																//Aelia currency switcher compatibility
																$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} elseif ($pro_price_to_display > $converted_amount ) {
																		$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} else {
																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																}

																$price_suffix = $product->get_price_suffix($converted_amount_act );

															} else {

																$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
															}
														} else {

															$newprice_act = $pro_price + $rule_role_price['discount_value'];

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															$price_suffix = $product->get_price_suffix($converted_amount_act );
														}

														

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

														if ($product->is_type('variable') ) {

															$newprice1 = $min_price - $rule_role_price['discount_value'];
															$newprice2 = $max_price - $rule_role_price['discount_value'];

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


															$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);

															if ($min_price == $max_price ) {

																$newprice_act = $pro_price - $rule_role_price['discount_value'];

																//Aelia currency switcher compatibility
																$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

																
																if ( 'incl' == $this->get_tax_price_display_mode() ) {
																	$newprice = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																} else {
																	$newprice = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																}

																//Aelia currency switcher compatibility
																$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} elseif ($pro_price_to_display > $converted_amount ) {
																		$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} else {
																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																}

																$price_suffix = $product->get_price_suffix($converted_amount_act );



															} else {

																$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
															}
														} else {

															$newprice_act = $pro_price - $rule_role_price['discount_value'];

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}

															$price_suffix = $product->get_price_suffix($converted_amount_act );

														}

														

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) { 

														if ($product->is_type('variable') ) { 

															$percent_price1 = $min_price * $rule_role_price['discount_value'] / 100;
															$newprice1      = $min_price - $percent_price1;

															$percent_price2 = $max_price * $rule_role_price['discount_value'] / 100;
															$newprice2      = $max_price - $percent_price2;

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


															$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);

															if ($min_price == $max_price ) { 

																
																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice_act = $pro_price - $percent_price;

																//Aelia currency switcher compatibility
																$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);


																if ( 'incl' == $this->get_tax_price_display_mode() ) {
																	$newprice = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																} else {
																	$newprice = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																}

																//Aelia currency switcher compatibility
																$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} elseif ($pro_price_to_display > $converted_amount ) {
																		$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} else {
																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																}

																$price_suffix = $product->get_price_suffix($converted_amount_act );

																

															} else {

																$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
															}
														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;
															$newprice_act  = $pro_price - $percent_price;

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															} elseif ($pro_price_to_display > $converted_amount ) {
																	$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															} else {
																$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
															}

															$price_suffix = $product->get_price_suffix($converted_amount_act );
														}

														

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {

														if ($product->is_type('variable') ) {


															$percent_price1 = $min_price * $rule_role_price['discount_value'] / 100;
															$newprice1      = $min_price + $percent_price1;

															$percent_price2 = $max_price * $rule_role_price['discount_value'] / 100;
															$newprice2      = $max_price + $percent_price2;

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice1,
																) );
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice2,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount_newprice1 = apply_filters('wc_aelia_cs_convert', $newprice1, $base_currency, $active_currency);


															$converted_amount_newprice2 = apply_filters('wc_aelia_cs_convert', $newprice2, $base_currency, $active_currency);

															if ($min_price == $max_price ) {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice_act = $pro_price + $percent_price;

																//Aelia currency switcher compatibility
																$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);

																
																if ( 'incl' == $this->get_tax_price_display_mode() ) {
																	$newprice = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																} else {
																	$newprice = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $newprice_act,
																	) );
																}

																//Aelia currency switcher compatibility
																$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																if (! empty($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price'] ) {

																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} elseif ($pro_price_to_display > $converted_amount ) {
																		$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																} else {
																	$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';
																}

																$price_suffix = $product->get_price_suffix($converted_amount_act );


															} else {

																$prices = '<ins class="highlight">' . wc_price($converted_amount_newprice1) . ' - ' . wc_price($converted_amount_newprice2) . '</ins>';
															}
														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;
															$newprice_act  = $pro_price + $percent_price;

															//Aelia currency switcher compatibility
															$converted_amount_act = apply_filters('wc_aelia_cs_convert', $newprice_act, $base_currency, $active_currency);
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice_act,
																) );
															}

															//Aelia currency switcher compatibility
															$converted_amount = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$prices = '<ins class="highlight">' . wc_price($converted_amount) . '</ins>';

															$price_suffix = $product->get_price_suffix($converted_amount_act );
														}

														

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}

														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}

													} else {

														if ($pro_price_to_display > $rule_role_price['discount_value'] ) {
															$prices = '<del class="strike">' . wc_price( $pro_price_to_display ) . '</del><ins class="highlight">' . wc_price($rule_role_price['discount_value']) . '</ins>';
														} else {
															$prices = '<ins class="highlight">' . wc_price($rule_role_price['discount_value']) . '</ins>';
														}

														$price_suffix = $product->get_price_suffix($pro_price );

														if ( ! empty( $price_suffix ) ) {

															$prices .= ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';

														}
														if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
															return $price;
														} else {
															return $prices;
														}
													}
												} else {

													$prices = $price;
													return $prices;
												}
											}
										}
									} else {

										$prices = $price;
										return $prices;
									}


								} else {

									$prices = $price;
									return $prices;

								}
							}
						}
					}
				}
			}
			
			if (is_product() && 'yes' == get_option('csp_enable_tire_price_table') && 'variable' !==  $product->get_type()) {
				return $price;
			} else {
				return $prices;
			}
		}

		public function af_csp_custom_price_range( $price, $product ) {

			$active_currency = get_woocommerce_currency();
			$base_currency   = get_option( 'woocommerce_currency' );


			//Aelia currency switcher compatibility
			$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);



			if (is_shop() || is_category() || is_product() || is_tag() || is_archive() ) {

			
				$customer_discount  = false;
				$user               = wp_get_current_user();
				$role               = ( array ) $user->roles;
				$price_for_discount = get_option('afb2b_discount_price');
				

				$first_role = current($user->roles);

				if (is_user_logged_in() ) {

					$current_user = wp_get_current_user();
					$first_role   = current($current_user->roles);
					
					if (!empty($price_for_discount[ $first_role ]) && 'sale' == $price_for_discount[ $first_role ] && !empty(get_post_meta($product->get_id(), '_sale_price', true))) {

						$pro_price = get_post_meta($product->get_id(), '_sale_price', true);

					} elseif (!empty($price_for_discount[ $first_role ]) && 'regular' === $price_for_discount[ $first_role ] && !empty(get_post_meta($product->get_id(), '_regular_price', true))) {

						$pro_price = get_post_meta($product->get_id(), '_regular_price', true);

					} else {

						$pro_price = get_post_meta($product->get_id(), '_price', true);
					}


				} elseif (!empty($price_for_discount['guest']) && 'sale' == $price_for_discount['guest'] && !empty(get_post_meta($product->get_id(), '_sale_price', true))) {


						$pro_price = get_post_meta($product->get_id(), '_sale_price', true);

				} elseif (!empty($price_for_discount['guest']) && 'regular' === $price_for_discount['guest'] && !empty(get_post_meta($product->get_id(), '_regular_price', true))) {

					$pro_price = get_post_meta($product->get_id(), '_regular_price', true);

				} else {

					$pro_price = get_post_meta($product->get_id(), '_price', true);

				}

				if (is_user_logged_in() ) {

					// get customer specific price
					$cus_base_price = get_post_meta($product->get_id(), '_cus_base_price', true);
		
					// get role base price
					$role_base_price = get_post_meta($product->get_id(), '_role_base_price', true);


					// get customer base price

					if (! empty($cus_base_price) ) {

						foreach ( $cus_base_price as $cus_price ) {

							if (isset($cus_price['customer_name']) && $user->ID == $cus_price['customer_name'] ) {

								if (( '' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) && 2 > $cus_price['min_qty']) {

									if ('fixed_price' == $cus_price['discount_type']  ) { 

										//$newprice = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $cus_price['discount_value'] ) );

										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $cus_price['discount_value'], $base_currency, $active_currency);


										return $prices;
									} 

									if ('fixed_increase' == $cus_price['discount_type'] ) {


										$newprice = $pro_price + $cus_price['discount_value'];

										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										return $prices;
									}

									if ('fixed_decrease' == $cus_price['discount_type'] ) {


										$newprice = $pro_price - $cus_price['discount_value'];

										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
										return $prices;

									} 

									if ('percentage_decrease' == $cus_price['discount_type'] ) {


										$percent_price = $pro_price * $cus_price['discount_value'] / 100;

										$newprice = $pro_price - $percent_price;

										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										return $prices;

									} elseif ('percentage_increase' == $cus_price['discount_type'] ) {


										$percent_price = $pro_price * $cus_price['discount_value'] / 100;

										$newprice = $pro_price + $percent_price;

										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										
										return $prices;

									} 
								} else {

									$prices = $price;
									//Aelia currency switcher compatibility
									$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
								}
							}
						}
					} else {

						$prices = $price;
						//Aelia currency switcher compatibility
						$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
					}

					// Role Based Pricing
					// chcek if there is customer specific pricing then role base pricing will not work.
					if (! $customer_discount ) {

						if (! empty($role_base_price) ) {

							foreach ( $role_base_price as $role_price ) {

								if (isset($role_price['user_role']) && current( $role ) == $role_price['user_role'] ) {

									if (( '' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) && 2 > $role_price['min_qty']) {

										if ('fixed_price' == $role_price['discount_type']  ) {
											
											//Aelia currency switcher compatibility
											$prices = apply_filters('wc_aelia_cs_convert', $role_price['discount_value'], $base_currency, $active_currency);
											return $prices;
										} 

										if ('fixed_increase' == $role_price['discount_type'] ) {


													$newprice = $pro_price + $role_price['discount_value'];

													//Aelia currency switcher compatibility
													$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
													return $prices;
										}

										if ('fixed_decrease' == $role_price['discount_type'] ) {


											$newprice = $pro_price - $role_price['discount_value'];

											//Aelia currency switcher compatibility
											$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
											return $prices;

										} 

										if ('percentage_decrease' == $role_price['discount_type'] ) {


											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;

											//Aelia currency switcher compatibility
											$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											return $prices;

										} elseif ('percentage_increase' == $role_price['discount_type'] ) {


											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;

											//Aelia currency switcher compatibility
											$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
											return $prices;

										} 
									} else {

										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
									}
								}
							}
						} else {

							//Aelia currency switcher compatibility
							$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
						}
					}

					// Rules
					if (true ) {

						if (empty($this->allfetchedrules) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if (! empty($all_rules) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;
								
								$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() )  ||  has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if (!empty($rbp_slected_brands)) {
									foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

										
										if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product->get_id() )  ||  has_term( $rbp_brand_slect, 'product_brand', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 


									}
								}

								


								if ($istrue ) {

									// get Rule customer specifc price
									$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

									// get role base price
									$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

								

									$customer_discount = false;

									if (! empty($rule_cus_base_price) ) {
										foreach ( $rule_cus_base_price as $rule_cus_price ) {

											if ($user->ID == $rule_cus_price['customer_name'] ) {

												if (( '' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) && 2 > $rule_cus_price['min_qty']) {

													if ('fixed_price' == $rule_cus_price['discount_type'] ) {
														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $rule_cus_price['discount_value'], $base_currency, $active_currency);
														return $prices;
													} elseif ('fixed_increase' == $rule_cus_price['discount_type'] ) {


														$newprice = $pro_price + $rule_cus_price['discount_value'];

														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
														return $prices;
													} elseif ('fixed_decrease' == $rule_cus_price['discount_type'] ) {


														$newprice = $pro_price - $rule_cus_price['discount_value'];

														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
														return $prices;

													} elseif ('percentage_decrease' == $rule_cus_price['discount_type'] ) {


														$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

														$newprice = $pro_price - $percent_price;

														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
														return $prices;

													} elseif ('percentage_increase' == $rule_cus_price['discount_type'] ) {


														$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

														$newprice = $pro_price + $percent_price;

														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
														return $prices;

													} else {

														$prices = $rule_cus_price['discount_value'];
														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $rule_cus_price['discount_value'], $base_currency, $active_currency);
													}
												} else {

													//Aelia currency switcher compatibility
													$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
												}
											}
										}
									} else {

										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
									}

									// Role Based Pricing
									// chcek if there is customer specific pricing then role base pricing will not work.
									if (true ) {

									
										if (! empty($rule_role_base_price) ) {
											foreach ( $rule_role_base_price as $rule_role_price ) {

												if (current( $role ) == $rule_role_price['user_role'] ) {

													if (( '' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) && 2 > $rule_role_price['min_qty']) {

														if ('fixed_price' == $rule_role_price['discount_type'] ) {
															$prices = $rule_role_price['discount_value'];
															//Aelia currency switcher compatibility
															$prices = apply_filters('wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency);
															return $prices;
														} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {


															$newprice = $pro_price + $rule_role_price['discount_value'];

															//Aelia currency switcher compatibility
															$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
															return $prices;
														} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {


															$newprice = $pro_price - $rule_role_price['discount_value'];

															//Aelia currency switcher compatibility
															$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
															return $prices;

														} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {


															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

															$newprice = $pro_price - $percent_price;

															//Aelia currency switcher compatibility
															$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
															return $prices;

														} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {


															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

															$newprice = $pro_price + $percent_price;

															//Aelia currency switcher compatibility
															$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
															return $prices;

														} else {

															$prices = $rule_role_price['discount_value'];
															//Aelia currency switcher compatibility
															$prices = apply_filters('wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency);
														}
													} else {

														$prices = $price;
														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
													}
												}
											}
										} else {

											$prices = $price;
											//Aelia currency switcher compatibility
											$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
										}

									}
								}
							}
						}
					}
				} else {

					// Role Based Pricing for guest
					// chcek if there is customer specific pricing then role base pricing will not work.
					if (true && !is_user_logged_in()) {

						// get role base price
						$role_base_price = get_post_meta($product->get_id(), '_role_base_price', true);
					
						if (! empty($role_base_price) ) {

							foreach ( $role_base_price as $role_price ) {

								if (isset($role_price['user_role']) && 'guest' == $role_price['user_role'] ) {

									if (( '' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) && 2 > $role_price['min_qty']) {

										if ('fixed_price' == $role_price['discount_type']  ) {
											$prices = $role_price['discount_value'];
											//Aelia currency switcher compatibility
											$prices = apply_filters('wc_aelia_cs_convert', $role_price['discount_value'], $base_currency, $active_currency);
											return $prices;
										} 

										if ('fixed_increase' == $role_price['discount_type'] ) {


											$newprice = $pro_price + $role_price['discount_value'];

											//Aelia currency switcher compatibility
											$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
											return $prices;
										}

										if ('fixed_decrease' == $role_price['discount_type'] ) {


											$newprice = $pro_price - $role_price['discount_value'];

											//Aelia currency switcher compatibility
											$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
											return $prices;

										} 

										if ('percentage_decrease' == $role_price['discount_type'] ) {


													$percent_price = $pro_price * $role_price['discount_value'] / 100;

													$newprice = $pro_price - $percent_price;

													//Aelia currency switcher compatibility
													$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

													return $prices;

										} elseif ('percentage_increase' == $role_price['discount_type'] ) {


												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price + $percent_price;

												//Aelia currency switcher compatibility
												$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
												return $prices;

										} 
									} else {

										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
									}
								}
							}
						} else {

							//Aelia currency switcher compatibility
							$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
						}

					} else {

						//Aelia currency switcher compatibility
						$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
					}

					// Rules - guest users
					if (true ) {

						if (empty($this->allfetchedrules) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if (! empty($all_rules) ) {
							foreach ( $all_rules as $rule ) {

								$istrue = false;
							

								$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() )  ||  has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if (!empty($rbp_slected_brands)) {
									foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

										if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product->get_id() )  ||  has_term( $rbp_brand_slect, 'product_brand', $product->get_parent_id() ) ) ) {

											$istrue = true;
										}  
									}
								}

								


								if ($istrue ) {

									// get role base price
									$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

									// Role Based Pricing
								

									
									if (! empty($rule_role_base_price) ) {
										foreach ( $rule_role_base_price as $rule_role_price ) {

											if ('guest' == $rule_role_price['user_role'] ) {

												if (( '' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) && 2 > $rule_role_price['min_qty']) {

													if ('fixed_price' == $rule_role_price['discount_type'] ) {
														$prices = $rule_role_price['discount_value'];
														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency);
														return $prices;
													} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {


														$newprice = $pro_price + $rule_role_price['discount_value'];

														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
														return $prices;
													} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {


														$newprice = $pro_price - $rule_role_price['discount_value'];

														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
														return $prices;

													} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {


														$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

														$newprice = $pro_price - $percent_price;

														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
														return $prices;

													} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {


														$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

														$newprice = $pro_price + $percent_price;

														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
														return $prices;

													} else {

														$prices = $rule_role_price['discount_value'];
														//Aelia currency switcher compatibility
														$prices = apply_filters('wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency);
													}
												} else {

													$prices = $price;
													//Aelia currency switcher compatibility
													$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
												}
											}
										}
									} else {

										$prices = $price;
										//Aelia currency switcher compatibility
										$prices = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
									}

								
								}
							}
						}
					}
				}

			}

			return $prices;
		}


		public function csp_validate_min_max_qty( $csppdata, $product_id, $qty = 1, $variation_id = 0 ) {

			$user               = wp_get_current_user();
			$role               = ( array ) $user->roles;
			$quantity           = 0;
			$customer_discount  = false;
			$role_discount      = false;
			$customer_discount1 = false;
			$role_discount1     = false;
			$first_min_qty      = '';
			$max_qty            = '';
			$parent_id          = 0;
			if (0 == $variation_id ) {

				$targeted_id = $product_id;
				$pro_id      = $product_id;

			} else {

				//Variable Product
				$targeted_id = $variation_id;
				$pro_id      = $variation_id;
			}

			foreach ( WC()->cart->get_cart() as $cart_item ) {

				if ('variation' === $cart_item['data']->get_type() ) {

					if ($variation_id === $cart_item['data']->get_id() ) {
						$oqty = $cart_item['quantity'];
						break;
					}
					
				} elseif ($product_id === $cart_item['data']->get_id() ) {


						$oqty = $cart_item['quantity'];
						break;
				}
			}
			// Displaying the quantity if targeted product is in cart
			if (! empty($oqty) ) {

				$old_qty = $oqty;
			} else {
				$old_qty = 0;
			}

			$total_quantity = $old_qty + $qty;

			if (is_user_logged_in() ) {

				// get customer specifc price
				$cus_base_price = get_post_meta($pro_id, '_cus_base_price', true);

				// get role base price
				$role_base_price = get_post_meta($pro_id, '_role_base_price', true);

				if (! empty($cus_base_price) ) {
					$n = 1;
					foreach ( $cus_base_price as $cus_price ) {

						
						if (isset($cus_price['customer_name']) && $user->ID == $cus_price['customer_name'] ) {

							if ('' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) {

								if ('' != $cus_price['min_qty'] ) {
									$min_qty = intval($cus_price['min_qty']);
									if (1==$n) {
											$first_min_qty = $min_qty;
											++$n;
									}
									$customer_discount = true;

								} else {
									$first_min_qty = '';
								}

								if ('' != $cus_price['max_qty'] ) {
										$max_qty           = intval($cus_price['max_qty']);
										$customer_discount = true;
									
								} else {
									$max_qty = '';
								}
							}
						}    
					}

					

					if ('' != $first_min_qty && $total_quantity < $first_min_qty ) { 
						$csppdata = false;
						
						$error_message = sprintf(get_option('csp_min_qty_error_msg'), $first_min_qty);
						$this->csp_wc_add_notice($error_message);
						
						return $csppdata;



					} elseif ('' != $max_qty && $total_quantity > $max_qty ) { 

						$csppdata = false;
						
						$error_message = sprintf(get_option('csp_max_qty_error_msg'), $max_qty);
						$this->csp_wc_add_notice($error_message);
						
						return $csppdata;

					} 

					
				}

				// Role Based Pricing
				// chcek if there is customer specific pricing then role base pricing will not work.
				if (! $customer_discount ) {

					if (! empty($role_base_price) ) {
						$n = 1;
						foreach ( $role_base_price as $role_price ) {

							
							if (isset($role_price['user_role']) && current( $role ) == $role_price['user_role'] ) {

								if ('' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) {

									if ('' != $role_price['min_qty']) {
										$min_qty = intval($role_price['min_qty']);
										if (1==$n) {
											$first_min_qty = $min_qty;
											++$n;
										}
										$role_discount = true;

									} else {
										$first_min_qty = '';
									}

									if ('' != $role_price['max_qty'] ) {
												$max_qty       = intval($role_price['max_qty']);
												$role_discount = true;
										
									} else {
										$max_qty = '';
									}
								}
							}
						}

						if ('' != $first_min_qty && $total_quantity < $first_min_qty ) {
							$csppdata      = false;
							$error_message = sprintf(get_option('csp_min_qty_error_msg'), $first_min_qty);
							$this->csp_wc_add_notice($error_message);
							
							return $csppdata;

						} elseif ('' != $max_qty && $total_quantity > $max_qty ) {

							$csppdata      = false;
							$error_message = sprintf(get_option('csp_max_qty_error_msg'), $max_qty);
							$this->csp_wc_add_notice($error_message);
							
							return $csppdata;

						}

						
					}

					   
				}

				//Rules
				if (false == $customer_discount && false == $role_discount ) {

					if (empty($this->allfetchedrules) ) {

						echo '';

					} else {

						$all_rules = $this->allfetchedrules;

					}


					if (! empty($all_rules) ) {
						foreach ( $all_rules as $rule ) {

							$istrue = false;                            

							$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
							$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
							$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
							$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

							if ('yes' == $applied_on_all_products ) {
								$istrue = true;
							} elseif (! empty($products) && ( in_array($pro_id, $products) || in_array($product_id, $products) ) ) {
								$istrue = true;
							}


							if (!empty($categories)) {
								foreach ( $categories as $cat ) {

									if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $pro_id )  ||  has_term( $cat, 'product_cat', $product_id ) ) ) {

										$istrue = true;
									} 
								}
							}


							if (!empty($rbp_slected_brands)) {
								foreach ( $rbp_slected_brands as $rbp_brand_slect ) {



									if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $pro_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $product_id ) ) ) {

										$istrue = true;
									} 
								}
							}

							


							if ($istrue ) {

								// get Rule customer specifc price
								$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

								// get role base price
								$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);


								if (! empty($rule_cus_base_price) ) {
									$n = 1;
									foreach ( $rule_cus_base_price as $rule_cus_price ) {

										if ($user->ID == $rule_cus_price['customer_name'] ) {

											if ('' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) {

												if ('' != $rule_cus_price['min_qty'] ) {
													$min_qty = intval($rule_cus_price['min_qty']);

													if (1==$n) {
														$first_min_qty = $min_qty;
														++$n;
													}

													$customer_discount1 = true;
												} else {
													$first_min_qty = '';
												}

												if ('' != $rule_cus_price['max_qty']  ) {
													$max_qty            = intval($rule_cus_price['max_qty']);
													$customer_discount1 = true;
												} else {
													$max_qty = '';
												}
												
											}
										}
									}

									if ('' != $first_min_qty && $total_quantity < $first_min_qty ) {
										$csppdata      = false;
										$error_message = sprintf(get_option('csp_min_qty_error_msg'), $first_min_qty);
										$this->csp_wc_add_notice($error_message);
										return $csppdata;

									} elseif ('' != $max_qty && $total_quantity > $max_qty ) {

										$csppdata      = false;
										$error_message = sprintf(get_option('csp_max_qty_error_msg'), $max_qty);
										$this->csp_wc_add_notice($error_message);
										return $csppdata;

									} 
								}

								// Role Based Pricing
								// chcek if there is customer specific pricing then role base pricing will not work.
								if (! $customer_discount1 ) {

									
									if (! empty($rule_role_base_price) ) {
										$n = 1;
										foreach ( $rule_role_base_price as $rule_role_price ) {

											if (current( $role ) == $rule_role_price['user_role'] ) {

												if ('' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) {

													if ('' != $rule_role_price['min_qty']  ) {
														$min_qty = intval($rule_role_price['min_qty']);

														if (1==$n) {
															$first_min_qty = $min_qty;
															++$n;
														}

													} else {
														$first_min_qty = '';
													}

													if ('' != $rule_role_price['max_qty'] ) {
																$max_qty = intval($rule_role_price['max_qty']);
													} else {
														$max_qty = '';
													}
													
												}
											}
										}

										if ('' != $first_min_qty && $total_quantity < $first_min_qty ) {
											$csppdata      = false;
											$error_message = sprintf(get_option('csp_min_qty_error_msg'), $first_min_qty);
											$this->csp_wc_add_notice($error_message);
											return $csppdata;

										} elseif ('' != $max_qty && $total_quantity > $max_qty ) {

											$csppdata      = false;
											$error_message = sprintf(get_option('csp_max_qty_error_msg'), $max_qty);
											$this->csp_wc_add_notice($error_message);
											return $csppdata;

										} else {
											return true;
										}
									}
								}
							}
						}
					}
				}

			} elseif (!is_user_logged_in()) {

				//guest
				



					// get role base price
					$role_base_price = get_post_meta($pro_id, '_role_base_price', true);

					// Role Based Pricing
					// chcek if there is customer specific pricing then role base pricing will not work.
				if (true ) {

					if (! empty($role_base_price) ) {
						$n = 1;
						foreach ( $role_base_price as $role_price ) {

								
							if (isset($role_price['user_role']) && 'guest' == $role_price['user_role'] ) {

								if ('' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) {

									if ('' != $role_price['min_qty'] ) {
										$min_qty = intval($role_price['min_qty']);
										if (1==$n) {
														$first_min_qty = $min_qty;
														++$n;
										}
										$role_discount = true;

									} else {
										$first_min_qty = '';
									}

									if ('' != $role_price['max_qty'] ) {
										$max_qty       = intval($role_price['max_qty']);
										$role_discount = true;
											
									} else {
										$max_qty = '';
									}
								}
							}
						}

						if ('' != $first_min_qty && $total_quantity < $first_min_qty ) {
							$csppdata      = false;
							$error_message = sprintf(get_option('csp_min_qty_error_msg'), $first_min_qty);
							$this->csp_wc_add_notice($error_message);
								
							return $csppdata;

						} elseif ('' != $max_qty && $total_quantity > $max_qty ) {

							$csppdata      = false;
							$error_message = sprintf(get_option('csp_max_qty_error_msg'), $max_qty);
							$this->csp_wc_add_notice($error_message);
								
							return $csppdata;

						} 

							
					}
				}


					// Rules - guest users
				if (!$role_discount ) {

					if (empty($this->allfetchedrules) ) {

						echo '';

					} else {

						$all_rules = $this->allfetchedrules;

					}

					if (! empty($all_rules) ) {
						foreach ( $all_rules as $rule ) {

							$istrue = false;
								

							$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
							$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
							$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
							$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

							if ('yes' == $applied_on_all_products ) {
								$istrue = true;
							} elseif (! empty($products) && ( in_array($pro_id, $products) || in_array($product_id, $products) ) ) {
								$istrue = true;
							}


							if (!empty($categories)) {
								foreach ( $categories as $cat ) {

									if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $pro_id )  ||  has_term( $cat, 'product_cat', $product_id ) ) ) {

										$istrue = true;
									} 
								}
							}



							if (!empty($rbp_slected_brands)) {
								foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

									if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $pro_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $product_id ) ) ) {

										$istrue = true;
									} 
								}
							}
								



							if ($istrue ) {

								// get role base price
								$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

								// Role Based Pricing
								// chcek if there is customer specific pricing then role base pricing will not work.
								if (true ) {

									if (! empty($rule_role_base_price) ) {
											$n = 1;
										foreach ( $rule_role_base_price as $rule_role_price ) {

											if ('guest' == $rule_role_price['user_role'] ) {

												if ('' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) {

													if ('' != $rule_role_price['min_qty']  ) {
														$min_qty = intval($rule_role_price['min_qty']);

														if (1==$n) {
															$first_min_qty = $min_qty;
															++$n;
														}

													} else {
														$first_min_qty = '';
													}

													if ('' != $rule_role_price['max_qty'] ) {
														$max_qty = intval($rule_role_price['max_qty']);
													} else {
														$max_qty = '';
													}
														
												}
											}
										}

										if ('' != $first_min_qty && $total_quantity < $first_min_qty ) {
											$csppdata      = false;
											$error_message = sprintf(get_option('csp_min_qty_error_msg'), $first_min_qty);
											$this->csp_wc_add_notice($error_message);
											return $csppdata;

										} elseif ('' != $max_qty && $old_qty + $qty > $max_qty ) {

											$csppdata      = false;
											$error_message = sprintf(get_option('csp_max_qty_error_msg'), $max_qty);
											$this->csp_wc_add_notice($error_message);
											return $csppdata;

										} else {
											return true;
										}
									}
								}
							}
						}
					}
				}
				
			}

			return $csppdata;
		}

		public function csp_update_cart_quantity_validation( $passed, $cart_item_key, $values, $qty ) {

			$user               = wp_get_current_user();
			$role               = ( array ) $user->roles;
			$quantity           = 0;
			$customer_discount  = false;
			$role_discount      = false;
			$customer_discount1 = false;
			$role_discount1     = false;

			if (0 == $values['variation_id'] ) {

				$product_id = $values['product_id'];
				$parent_id  = 0;
			} else {

				$product_id = $values['variation_id'];
				$parent_id  = $values['product_id'];

			}

			$pro = wc_get_product($product_id);

			if (is_user_logged_in() ) {

				// get customer specifc price
				
				$cus_base_price = get_post_meta($product_id, '_cus_base_price', true);

				// get role base price
				$role_base_price = get_post_meta($product_id, '_role_base_price', true);

				

				if (! empty($cus_base_price) ) {
					$n = 1;
					foreach ( $cus_base_price as $cus_price ) {

						if (isset($cus_price['customer_name']) && $user->ID == $cus_price['customer_name'] ) {

							if ('' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) {

								if ('' != $cus_price['min_qty'] || 0 != $cus_price['min_qty'] ) {
									$min_qty = intval($cus_price['min_qty']);
									if (1==$n) {
											$first_min_qty = $min_qty;
											++$n;
									}
									$customer_discount = true;
								} else {
									$min_qty = '';
								}

								if ('' != $cus_price['max_qty'] || 0 != $cus_price['max_qty'] ) {
										$max_qty           = intval($cus_price['max_qty']);
										$customer_discount = true;
								} else {
									$max_qty = '';
								}

								
							}
						}
					}

					if (( '' != $first_min_qty && $qty < $first_min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
						$passed        = false;
						$arr           = array(
							'%pro' => $pro->get_title(),
							'%min' => $first_min_qty,
							'%max' => $max_qty,
						);
						$word          = get_option('csp_update_cart_error_msg');
						$error_message = strtr($word, $arr);

						$this->csp_wc_add_notice($error_message);
						return $passed;

					} else {
						return $passed;
					}
				}


				// Role Based Pricing
				// chcek if there is customer specific pricing then role base pricing will not work.
				if (!$customer_discount ) {

					if (! empty($role_base_price) ) {
						$n = 1;
						foreach ( $role_base_price as $role_price ) {

							if (isset($role_price['user_role']) && current( $role ) == $role_price['user_role'] ) {

								if ('' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) {

									if ('' != $role_price['min_qty'] || 0 != $role_price['min_qty'] ) {
										$min_qty = intval($role_price['min_qty']);
										if (1==$n) {
											$first_min_qty = $min_qty;
											++$n;
										}
										$role_discount = true;
									} else {
										$min_qty = '';
									}

									if ('' != $role_price['max_qty'] || 0 != $role_price['max_qty'] ) {
												$max_qty       = intval($role_price['max_qty']);
												$role_discount = true;
									} else {
										$max_qty = '';
									}

									
								}
							}
						}

						if (( '' != $first_min_qty && $qty < $first_min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
							$passed        = false;
							$arr           = array(
								'%pro' => $pro->get_title(),
								'%min' => $first_min_qty,
								'%max' => $max_qty,
							);
							$word          = get_option('csp_update_cart_error_msg');
							$error_message = strtr($word, $arr);

							$this->csp_wc_add_notice($error_message);
							return $passed;

						} else {
							return $passed;
						}
					}
					   
				}

				//Rules
				if (false == $customer_discount && false == $role_discount ) {

					if (empty($this->allfetchedrules) ) {

						echo '';

					} else {

						$all_rules = $this->allfetchedrules;

					}

					if (! empty($all_rules) ) {

						foreach ( $all_rules as $rule ) {

							$istrue = false;
							

							$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
							$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
							$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
							$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

							if ('yes' == $applied_on_all_products ) {
								$istrue = true;
							} elseif (! empty($products) && ( in_array($product_id, $products) || in_array($parent_id, $products) ) ) {
								$istrue = true;
							}


							if (!empty($categories)) {
								foreach ( $categories as $cat ) {

									if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product_id )  ||  has_term( $cat, 'product_cat', $parent_id ) ) ) {

										$istrue = true;
									} 
								}
							}

							if (!empty($rbp_slected_brands)) {
								foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

									if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

										$istrue = true;
									} 
								}
							}





							if ($istrue) {


								// get Rule customer specifc price
								$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

								// get role base price
								$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);


								if (! empty($rule_cus_base_price) ) {
									$n =1;
									foreach ( $rule_cus_base_price as $rule_cus_price ) {

										if ($user->ID == $rule_cus_price['customer_name'] ) {

											if ('' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) {

												if ('' != $rule_cus_price['min_qty'] || 0 != $rule_cus_price['min_qty'] ) {
													$min_qty = intval($rule_cus_price['min_qty']);
													if (1==$n) {
														$first_min_qty = $min_qty;
														++$n;
													}
													$customer_discount1 = true;
												} else {
													$min_qty = '';
												}

												if ('' != $rule_cus_price['max_qty'] || 0 != $rule_cus_price['max_qty'] ) {
															$max_qty            = intval($rule_cus_price['max_qty']);
															$customer_discount1 = true;
												} else {
													$max_qty = '';
												}

												
											}
										}
									}


									if (( '' != $first_min_qty && $qty < $first_min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
										$passed                                    = false;
										$arr                                       = array(
											'%pro' => $pro->get_title(),
											'%min' => $first_min_qty,
											'%max' => $max_qty,
										);
																	$word          = get_option('csp_update_cart_error_msg');
																	$error_message = strtr($word, $arr);

																	$this->csp_wc_add_notice($error_message);
																	return $passed;

									} else {
										return $passed;
									}
								}

								// Role Based Pricing
								// chcek if there is customer specific pricing then role base pricing will not work.
								if (!$customer_discount1 ) {

									
									if (! empty($rule_role_base_price) ) {
										$n =1;
										foreach ( $rule_role_base_price as $rule_role_price ) {

											if (current( $role ) == $rule_role_price['user_role'] ) {

												if ('' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) {

													if ('' != $rule_role_price['min_qty'] || 0 != $rule_role_price['min_qty'] ) {
														$min_qty = intval($rule_role_price['min_qty']);
														if (1==$n) {
															$first_min_qty = $min_qty;
															++$n;
														}
													} else {
														$min_qty = '';
													}

													if ('' != $rule_role_price['max_qty'] || 0 != $rule_role_price['max_qty'] ) {
																$max_qty = intval($rule_role_price['max_qty']);
													} else {
														$max_qty = '';
													}

													
												}
											}
										}


										if (( '' != $first_min_qty && $qty < $first_min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
											$passed                                    = false;
											$arr                                       = array(
												'%pro' => $pro->get_title(),
												'%min' => $first_min_qty,
												'%max' => $max_qty,
											);
																		$word          = get_option('csp_update_cart_error_msg');
																		$error_message = strtr($word, $arr);

																		$this->csp_wc_add_notice($error_message);
																		return $passed;

										} else {
											return $passed;
										}
									}
								}
							}
						}
					}

				}

			} elseif (!is_user_logged_in() ) {

				//Guest
				


					// get role base price
					$role_base_price = get_post_meta($product_id, '_role_base_price', true);

					// Role Based Pricing
					// chcek if there is customer specific pricing then role base pricing will not work.
				if (true ) {

					if (! empty($role_base_price) ) {
						$n = 1;
						foreach ( $role_base_price as $role_price ) {

							if (isset($role_price['user_role']) && 'guest' == $role_price['user_role'] ) {

								if ('' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) {

									if ('' != $role_price['min_qty'] || 0 != $role_price['min_qty'] ) {
										$min_qty = intval($role_price['min_qty']);
										if (1==$n) {
														$first_min_qty = $min_qty;
														++$n;
										}
										$role_discount = true;
									} else {
										$min_qty = '';
									}

									if ('' != $role_price['max_qty'] || 0 != $role_price['max_qty'] ) {
										$max_qty       = intval($role_price['max_qty']);
										$role_discount = true;
									} else {
										$max_qty = '';
									}
								}
							}
						}

						if (( '' != $first_min_qty && $qty < $first_min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
							$passed        = false;
							$arr           = array(
								'%pro' => $pro->get_title(),
								'%min' => $first_min_qty,
								'%max' => $max_qty,
							);
							$word          = get_option('csp_update_cart_error_msg');
							$error_message = strtr($word, $arr);

							$this->csp_wc_add_notice($error_message);
							return $passed;

						} else {
							return $passed;
						}
					}
				}


				if (!$role_discount ) {

					if (empty($this->allfetchedrules) ) {

						echo '';

					} else {

						$all_rules = $this->allfetchedrules;

					}

					if (! empty($all_rules) ) {
						foreach ( $all_rules as $rule ) {

							$istrue = false;
								

							$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
							$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
							$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
							$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

							if ('yes' == $applied_on_all_products ) {
								$istrue = true;
							} elseif (! empty($products) && ( in_array($product_id, $products) || in_array($parent_id, $products) ) ) {
								$istrue = true;
							}


							if (!empty($categories)) {
								foreach ( $categories as $cat ) {

									if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product_id )  ||  has_term( $cat, 'product_cat', $parent_id ) ) ) {

										$istrue = true;
									} 
								}
							}


							if (!empty($rbp_slected_brands)) {
								foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

									if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

										$istrue = true;
									} 
								}
							}

								


							if ($istrue ) {

								// get role base price
								$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

								// Role Based Pricing
								// chcek if there is customer specific pricing then role base pricing will not work.
								if (true ) {

										
									if (! empty($rule_role_base_price) ) {
											$n =1;
										foreach ( $rule_role_base_price as $rule_role_price ) {

											if ('guest' == $rule_role_price['user_role'] ) {

												if ('' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) {

													if ('' != $rule_role_price['min_qty'] || 0 != $rule_role_price['min_qty'] ) {
														$min_qty = intval($rule_role_price['min_qty']);
														if (1==$n) {
															$first_min_qty = $min_qty;
															++$n;
														}
													} else {
														$min_qty = '';
													}

													if ('' != $rule_role_price['max_qty'] || 0 != $rule_role_price['max_qty'] ) {
														$max_qty = intval($rule_role_price['max_qty']);
													} else {
														$max_qty = '';
													}

														
												}
											}
										}


										if (( '' != $first_min_qty && $qty < $first_min_qty ) || ( '' != $max_qty && $qty > $max_qty ) ) {
											$passed                            = false;
											$arr                               = array(
												'%pro' => $pro->get_title(),
												'%min' => $first_min_qty,
												'%max' => $max_qty,
											);
																$word          = get_option('csp_update_cart_error_msg');
																$error_message = strtr($word, $arr);

																$this->csp_wc_add_notice($error_message);
																return $passed;

										} else {
											return $passed;
										}
									}
								}
							}
						}
					}
				}
				
			}
			

			return $passed;
		}

		public function csp_wc_add_notice( $string, $type = 'error' ) {

			global $woocommerce;
			if (version_compare($woocommerce->version, 2.1, '>=') ) {
				wc_add_notice($string, $type);
			} else {
				$woocommerce->add_error($string);
			}
		}

		
		
		public function af_csp_show_discount() {

			$csp_table_enable                    = get_option('csp_enable_tire_price_table');
			$afb2b_role_data_for_template_design = array();


			if (!$csp_table_enable ) {
				return;
			}

			global $product;
			$user               = wp_get_current_user();
			$role               = ( array ) $user->roles;
			$first_role         = current($user->roles);
			$customer_discount  = false;
			$role_discount      = false;
			$customer_discount1 = false;
			//$pro_price          = (int) get_post_meta($product->get_id(), '_price', true);
			$price_for_discount = get_option('afb2b_discount_price');

			$active_currency = get_woocommerce_currency();
			$base_currency   = get_option( 'woocommerce_currency' );

			if ('variable' != $product->get_type() ) {

				if (is_user_logged_in() ) {

					if (!empty($price_for_discount[ $first_role ]) && 'sale' == $price_for_discount[ $first_role ] && !empty(get_post_meta($product->get_id(), '_sale_price', true))) {

						$pro_price = get_post_meta($product->get_id(), '_sale_price', true);

					} elseif (!empty($price_for_discount[ $first_role ]) && 'regular' === $price_for_discount[ $first_role ] && !empty(get_post_meta($product->get_id(), '_regular_price', true))) {

						$pro_price = get_post_meta($product->get_id(), '_regular_price', true);

					} else {

						$pro_price = get_post_meta($product->get_id(), '_price', true);
					}

					$pro_price_catering_tax = 'incl' == $this->get_tax_price_display_mode()?wc_get_price_including_tax( $product, array(
						'qty'   => 1,
						'price' => $pro_price,
					) ):wc_get_price_excluding_tax( $product, array(
						'qty'   => 1,
						'price' => $pro_price,
					) );

					//Products other than variable product
					// get customer specific price
					$cus_base_price = get_post_meta($product->get_id(), '_cus_base_price', true);

					// get role base price
					$role_base_price = get_post_meta($product->get_id(), '_role_base_price', true);

					if (! empty($cus_base_price) ) {

						foreach ( $cus_base_price as $cus_price ) {

							if (isset($cus_price['customer_name']) && $user->ID == $cus_price['customer_name'] ) {

								if ('' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) {

									
									$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

									//Fixed Price
									if ('fixed_price' == $cus_price['discount_type'] ) {


										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $cus_price['discount_value'],
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $cus_price['discount_value'],
											) );
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);



										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $cus_price['min_qty'],
											'max_qty'      => $cus_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
										);

										$customer_discount = true;

									} elseif ('fixed_increase' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
											$newprice = $newprice + $cus_price['discount_value'];
										} else {

											$newprice = $pro_price + $cus_price['discount_value'];
										}

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}

										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $cus_price['min_qty'],
											'max_qty'      => $cus_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$customer_discount = true;

									} elseif ('fixed_decrease' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$newprice = $pro_price - $cus_price['discount_value'];
										}

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}

										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $cus_price['min_qty'],
											'max_qty'      => $cus_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
										);
										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
										
										$customer_discount = true;

									} elseif ('percentage_decrease' == $cus_price['discount_type'] ) {

										if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;
										}

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $cus_price['min_qty'],
											'max_qty'      => $cus_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
										);


										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$customer_discount = true;

									} elseif ('percentage_increase' == $cus_price['discount_type'] ) {

										if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}

										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $cus_price['min_qty'],
											'max_qty'      => $cus_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
										);
										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$customer_discount = true;
									}
								}
							}
						}
					}


					// Role Based Pricing
					// chcek if there is customer specific pricing then role base pricing will not work.
					if (! $customer_discount ) {

						if (! empty($role_base_price) ) {

							foreach ( $role_base_price as $role_price ) {

								if (isset($role_price['user_role']) && current( $role ) == $role_price['user_role'] ) {

									if ('' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) {

										$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

										//Fixed Price
										if ('fixed_price' == $role_price['discount_type'] ) {

											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $role_price['discount_value'],
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $role_price['discount_value'],
												) );
											}
											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);
											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$role_discount = true;

										} elseif ('fixed_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												$newprice = $newprice + $role_price['discount_value'];
											} else {

												$newprice = $pro_price + $role_price['discount_value'];
											}

											
											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											}

											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);
											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$role_discount = true;

										} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
											} else {

												$newprice = $pro_price - $role_price['discount_value'];
											}

											
											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											}
											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);
											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$role_discount = true;

										} elseif ('percentage_decrease' == $role_price['discount_type'] ) {

											if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;

											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;
												$newprice      = $pro_price - $percent_price;
											}

											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											}

											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
											
											$role_discount = true;

										} elseif ('percentage_increase' == $role_price['discount_type'] ) {

											if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;

											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;
												$newprice      = $pro_price + $percent_price;
											}

											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											}
											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
											
											$role_discount = true;
										}
									}
								}
							}
						}
					}


					//Rules
					if (false == $customer_discount && false == $role_discount ) {


						if (empty($this->allfetchedrules) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if (! empty($all_rules) ) {

							$one_rule_implemented = false;

							foreach ( $all_rules as $rule ) {

								$istrue = false;

								if ( $one_rule_implemented ) {
									return;
								}

								$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
									$istrue = true;
								}

								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() )  ||  has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}

								if (!empty($rbp_slected_brands)) {
									foreach ( $rbp_slected_brands as $rbp_brand_slect ) {


										if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product->get_id() )  ||  has_term( $rbp_brand_slect, 'product_brand', $product->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}


								if ($istrue ) {
									// get Rule customer specifc price
									$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

									// get role base price
									$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);


									if (! empty($rule_cus_base_price) ) {


										foreach ( $rule_cus_base_price as $rule_cus_price ) {

											if ($user->ID == $rule_cus_price['customer_name'] ) {

												$one_rule_implemented = true;

												if ('' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) {

													$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

													//Fixed Price
													if ('fixed_price' == $rule_cus_price['discount_type'] ) { 

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_cus_price['discount_value'],
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_cus_price['discount_value'],
															) );
														}
														
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_cus_price['min_qty'],
															'max_qty'                   => $rule_cus_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
														);
														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$customer_discount1 = true;

													} elseif ('fixed_increase' == $rule_cus_price['discount_type'] ) {

														if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;
															$newprice = $newprice + $rule_cus_price['discount_value'];
														} else {

															$newprice = $pro_price + $rule_cus_price['discount_value'];
														}
														
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_cus_price['min_qty'],
															'max_qty'                   => $rule_cus_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$customer_discount1 = true;

													} elseif ('fixed_decrease' == $rule_cus_price['discount_type'] ) {

														if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;
														} else {

															$newprice = $pro_price - $rule_cus_price['discount_value'];
														}

														
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_cus_price['min_qty'],
															'max_qty'                   => $rule_cus_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
														);
														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$customer_discount1 = true;

													} elseif ('percentage_decrease' == $rule_cus_price['discount_type'] ) {

														if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

															$newprice = $pro_price - $percent_price;
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_cus_price['min_qty'],
															'max_qty'                   => $rule_cus_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
														);
														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$customer_discount1 = true;

													} elseif ('percentage_increase' == $rule_cus_price['discount_type'] ) {

														if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

															$newprice = $pro_price + $percent_price;
														}

														   
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_cus_price['min_qty'],
															'max_qty'                   => $rule_cus_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
														);
														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														$customer_discount1 = true;
													}
												}
											}
										}
									}


									// Rule Role Based Pricing
									// chcek if there is customer specific pricing then role base pricing will not work.
									if (! $customer_discount1 ) {

										if (! empty($rule_role_base_price) ) {


											foreach ( $rule_role_base_price as $rule_role_price ) {

												if ( current( $role ) == $rule_role_price['user_role'] ) {

													$one_rule_implemented = true;

													if ('' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) {


														$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

														//Fixed Price
														if ('fixed_price' == $rule_role_price['discount_type'] ) {

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);
															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$role_discount1 = true;

														} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																$newprice = $newprice + $rule_role_price['discount_value'];
															} else {

																$newprice = $pro_price + $rule_role_price['discount_value'];
															}
															
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);          

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$role_discount1 = true;

														} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
															} else {

																$newprice = $pro_price - $rule_role_price['discount_value'];
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);  

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$role_discount1 = true;

														} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {

															if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price - $percent_price;
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);  
															
															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$role_discount1 = true;

														} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {

															if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price + $percent_price;
															}

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															}

															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);  
															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);                                                          

															$role_discount1 = true;

														}

													}
												}
											}
										}
									}
								}
							}
						}
					}

				} elseif (!is_user_logged_in() ) {

					// User is not logged in
					

					if (!empty($price_for_discount['guest']) && 'sale' == $price_for_discount['guest'] && !empty(get_post_meta($product->get_id(), '_sale_price', true))) {

						$pro_price = get_post_meta($product->get_id(), '_sale_price', true);

					} elseif (!empty($price_for_discount['guest']) && 'regular' === $price_for_discount['guest'] && !empty(get_post_meta($product->get_id(), '_regular_price', true))) {

						$pro_price = get_post_meta($product->get_id(), '_regular_price', true);

					} else {

						$pro_price = get_post_meta($product->get_id(), '_price', true);
					}

					$pro_price_catering_tax = 'incl' == $this->get_tax_price_display_mode()?wc_get_price_including_tax( $product, array(
						'qty'   => 1,
						'price' => $pro_price,
					) ):wc_get_price_excluding_tax( $product, array(
						'qty'   => 1,
						'price' => $pro_price,
					) );

					// Role Based Pricing for guest
					if (true ) {

							// get role base price for guest
							$role_base_price = get_post_meta($product->get_id(), '_role_base_price', true);
							
						if (! empty($role_base_price) ) {

							foreach ( $role_base_price as $role_price ) {

								if (isset($role_price['user_role']) && 'guest' == $role_price['user_role'] ) {

									if ('' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) {

											

										$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

										//Fixed Price
										if ('fixed_price' == $role_price['discount_type'] ) {

											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $role_price['discount_value'],
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $role_price['discount_value'],
												) );
											}

											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);  
											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$role_discount = true;

										} elseif ('fixed_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												$newprice = $newprice + $role_price['discount_value'];
											} else {

												$newprice = $pro_price + $role_price['discount_value'];
											}


											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											}
											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$role_discount = true;

										} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
											} else {

												$newprice = $pro_price - $role_price['discount_value'];
											}

												
											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											}
											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$role_discount = true;

										} elseif ('percentage_decrease' == $role_price['discount_type'] ) {

											if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;

											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price - $percent_price;
											}

												
											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											}
											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$role_discount = true;

										} elseif ('percentage_increase' == $role_price['discount_type'] ) {


											if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;

											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price + $percent_price;
											}


												
											if ( 'incl' == $this->get_tax_price_display_mode() ) {
												$newprice = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											} else {
												$newprice = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $newprice,
												) );
											}
											$afb2b_role_data_for_template_design[] = array(
												'min_qty' => $role_price['min_qty'],
												'max_qty' => $role_price['max_qty'],
												'discounted_price' => $newprice,
												'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
												'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
											);

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$role_discount = true;

										}
									}
								}
							}
						}

						// Rules - guest users
						if (false == $role_discount  ) {

							if (empty($this->allfetchedrules) ) {

								echo '';

							} else {

								$all_rules = $this->allfetchedrules;

							}

							if (! empty($all_rules) ) {

								$one_rule_implemented = false;

								foreach ( $all_rules as $rule ) {

									$istrue = false;
										
									if ( $one_rule_implemented ) {
										return;
									}

									$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
									$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
									$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
									$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

									if ('yes' == $applied_on_all_products ) {
										$istrue = true;
									} elseif (! empty($products) && ( in_array($product->get_id(), $products) || in_array($product->get_parent_id(), $products) ) ) {
										$istrue = true;
									}


									if (!empty($categories)) {
										foreach ( $categories as $cat ) {

											if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product->get_id() )  ||  has_term( $cat, 'product_cat', $product->get_parent_id() ) ) ) {

												$istrue = true;
											} 
										}
									}

									if (!empty($rbp_slected_brands)) {
										foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

											if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product->get_id() )  ||  has_term( $rbp_brand_slect, 'product_brand', $product->get_parent_id() ) ) ) {

												$istrue = true;
											} 
										}
									}


									if ($istrue ) {

										// get rule role base price for guest
										$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

										if (! empty($rule_role_base_price) ) {


											foreach ( $rule_role_base_price as $rule_role_price ) {

												if ('guest' == $rule_role_price['user_role'] ) {

													if ('' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) {


														$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

														//Fixed Price
														if ('fixed_price' == $rule_role_price['discount_type'] ) {

															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_role_price['discount_value'],
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																$newprice = $newprice + $rule_role_price['discount_value'];
															} else {

																$newprice = $pro_price + $rule_role_price['discount_value'];
															}


																
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
															} else {

																$newprice = $pro_price - $rule_role_price['discount_value'];
															}

																
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {


															if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price - $percent_price;
															}


																   
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {


															if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price + $percent_price;
															}

																  
															if ( 'incl' == $this->get_tax_price_display_mode() ) {
																$newprice = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															} else {
																$newprice = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $newprice,
																) );
															}
															$afb2b_role_data_for_template_design[] = array(
																'min_qty'                   => $rule_role_price['min_qty'],
																'max_qty'                   => $rule_role_price['max_qty'],
																'discounted_price'          => $newprice,
																'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
																'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
															);

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
					
				}
			}

			if (!empty($afb2b_role_data_for_template_design)) {
				include_once AFB2B_PLUGIN_DIR . 'templates/pricing_design/afb2b_role_pricing_design_template.php';          
			}
		}


		public function af_csp_custom_variation_price_text( $data, $product, $variation ) {

			$csp_table_enable = get_option('csp_enable_tire_price_table');

			$afb2b_role_data_for_template_design = array();

			if (!$csp_table_enable ) {

				return $data;
			} 

			$user               = wp_get_current_user();
			$role               = ( array ) $user->roles;
			$customer_discount  = false;
			$role_discount      = false;
			$customer_discount1 = false;
			$first_role         = current($user->roles);
			$price_for_discount = get_option('afb2b_discount_price');
			//$pro_price          = get_post_meta($variation->get_id(), '_price', true);
			$msg_data = '';

			$active_currency = get_woocommerce_currency();
			$base_currency   = get_option( 'woocommerce_currency' );

			//$data['price_html'] .= '<span class="4xcb"> ' . $price . '</span>';

			if (is_user_logged_in() ) {


				if (!empty($price_for_discount[ $first_role ]) && 'sale' == $price_for_discount[ $first_role ] && !empty(get_post_meta($variation->get_id(), '_sale_price', true))) {

					$pro_price = get_post_meta($variation->get_id(), '_sale_price', true);

				} elseif (!empty($price_for_discount[ $first_role ]) && 'regular' === $price_for_discount[ $first_role ] && !empty(get_post_meta($variation->get_id(), '_regular_price', true))) {

					$pro_price = get_post_meta($variation->get_id(), '_regular_price', true);

				} else {

					$pro_price = get_post_meta($variation->get_id(), '_price', true);
				}

				$pro_price_catering_tax = 'incl' == $this->get_tax_price_display_mode()?wc_get_price_including_tax( $product, array(
					'qty'   => 1,
					'price' => $pro_price,
				) ):wc_get_price_excluding_tax( $product, array(
					'qty'   => 1,
					'price' => $pro_price,
				) );

				//Products other than variable product
				// get customer specifc price
				$cus_base_price = get_post_meta($variation->get_id(), '_cus_base_price', true);

				// get role base price
				$role_base_price = get_post_meta($variation->get_id(), '_role_base_price', true);


				if (! empty($cus_base_price) ) {

					foreach ( $cus_base_price as $cus_price ) {

						if (isset($cus_price['customer_name']) && $user->ID == $cus_price['customer_name'] ) {

							if ('' != $cus_price['discount_value'] || 0 != $cus_price['discount_value'] ) {

								$customer_discount = true;

								$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

								//Fixed Price
								if ('fixed_price' == $cus_price['discount_type'] ) {

									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $cus_price['discount_value'],
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $cus_price['discount_value'],
										) );
									}
									$afb2b_role_data_for_template_design[] = array(
										'min_qty'          => $cus_price['min_qty'],
										'max_qty'          => $cus_price['max_qty'],
										'discounted_price' => $newprice,
										'saved_amount'     => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
										'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
									);

									//Aelia currency switcher compatibility
									$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

								} elseif ('fixed_increase' == $cus_price['discount_type'] ) {

									if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

										$newprice = 0;
										$newprice = $newprice + $cus_price['discount_value'];
									} else {

										$newprice = $pro_price + $cus_price['discount_value'];
									}

									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice,
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice,
										) );
									}
									$afb2b_role_data_for_template_design[] = array(
										'min_qty'          => $cus_price['min_qty'],
										'max_qty'          => $cus_price['max_qty'],
										'discounted_price' => $newprice,
										'saved_amount'     => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
										'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
									);

									//Aelia currency switcher compatibility
									$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

								} elseif ('fixed_decrease' == $cus_price['discount_type'] ) {

									if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

										$newprice = 0;
									} else {

										$newprice = $pro_price - $cus_price['discount_value'];
									}

									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice,
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice,
										) );
									}
									$afb2b_role_data_for_template_design[] = array(
										'min_qty'          => $cus_price['min_qty'],
										'max_qty'          => $cus_price['max_qty'],
										'discounted_price' => $newprice,
										'saved_amount'     => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
										'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
									);

									//Aelia currency switcher compatibility
									$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

								} elseif ('percentage_decrease' == $cus_price['discount_type'] ) {

									if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

										$newprice = 0;

									} else {

										$percent_price = $pro_price * $cus_price['discount_value'] / 100;

										$newprice = $pro_price - $percent_price;
									}

									
									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice,
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice,
										) );
									}
									$afb2b_role_data_for_template_design[] = array(
										'min_qty'          => $cus_price['min_qty'],
										'max_qty'          => $cus_price['max_qty'],
										'discounted_price' => $newprice,
										'saved_amount'     => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
										'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
									);

									//Aelia currency switcher compatibility
									$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

								} elseif ('percentage_increase' == $cus_price['discount_type'] ) {


									if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

										$newprice = 0;

									} else {

										$percent_price = $pro_price * $cus_price['discount_value'] / 100;

										$newprice = $pro_price + $percent_price;
									}


									
									if ( 'incl' == $this->get_tax_price_display_mode() ) {
										$newprice = wc_get_price_including_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice,
										) );
									} else {
										$newprice = wc_get_price_excluding_tax( $product, array(
											'qty'   => 1,
											'price' => $newprice,
										) );
									}
									$afb2b_role_data_for_template_design[] = array(
										'min_qty'          => $cus_price['min_qty'],
										'max_qty'          => $cus_price['max_qty'],
										'discounted_price' => $newprice,
										'saved_amount'     => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
										'replace_original_price' => isset($cus_price['replace_orignal_price']) && 'yes' == $cus_price['replace_orignal_price']?'yes':'no',
									);

									//Aelia currency switcher compatibility
									$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
								}
							}
						}
					}
				}


				// Role Based Pricing
				// check if there is customer specific pricing then role base pricing will not work.
				if (! $customer_discount ) {

					if (! empty($role_base_price) ) {

						foreach ( $role_base_price as $role_price ) {

							if (isset($role_price['user_role']) && current( $role ) == $role_price['user_role'] ) {

								if ('' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) {

									$role_discount = true;

									$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

									//Fixed Price
									if ('fixed_price' == $role_price['discount_type'] ) {

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $role_price['discount_value'],
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $role_price['discount_value'],
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									} elseif ('fixed_increase' == $role_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
											$newprice = $newprice + $role_price['discount_value'];
										} else {

											$newprice = $pro_price + $role_price['discount_value'];
										}

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$newprice = $pro_price - $role_price['discount_value'];
										}

										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);
										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									} elseif ('percentage_decrease' == $role_price['discount_type'] ) {


										if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;
										}

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);
										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									} elseif ('percentage_increase' == $role_price['discount_type'] ) {

										if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									}
								}
							}
						}
					}
				}


				//Rules
				if (false == $customer_discount && false == $role_discount ) {


					if (empty($this->allfetchedrules) ) {

						echo '';

					} else {

						$all_rules = $this->allfetchedrules;

					}

					if (! empty($all_rules) ) {

						foreach ( $all_rules as $rule ) {

							$istrue = false;
														
							$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
							$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
							$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
							$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

							if ('yes' == $applied_on_all_products ) {
								$istrue = true;
							} elseif (! empty($products) && ( in_array($variation->get_id(), $products) || in_array($variation->get_parent_id(), $products) ) ) {
								$istrue = true;
							}


							if (!empty($categories)) {
								foreach ( $categories as $cat ) {

									if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $variation->get_id() )  ||  has_term( $cat, 'product_cat', $variation->get_parent_id() ) ) ) {

										$istrue = true;
									} 
								}
							}

							if (!empty($rbp_slected_brands)) {
								foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

									

									if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $variation->get_id() )  ||  has_term( $rbp_brand_slect, 'product_brand', $variation->get_parent_id() ) ) ) {

										$istrue = true;
									} 
								}
							}




							if ($istrue ) {

								// get Rule customer specifc price
								$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

								// get role base price
								$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);


								if (! empty($rule_cus_base_price) ) {


									foreach ( $rule_cus_base_price as $rule_cus_price ) {

										if ($user->ID == $rule_cus_price['customer_name'] ) {

											if ('' != $rule_cus_price['discount_value'] || 0 != $rule_cus_price['discount_value'] ) {

												$customer_discount1 = true;


												$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

												//Fixed Price
												if ('fixed_price' == $rule_cus_price['discount_type'] ) {

													if ( 'incl' == $this->get_tax_price_display_mode() ) {
														$newprice = wc_get_price_including_tax( $product, array(
															'qty'   => 1,
															'price' => $rule_cus_price['discount_value'],
														) );
													} else {
														$newprice = wc_get_price_excluding_tax( $product, array(
															'qty'   => 1,
															'price' => $rule_cus_price['discount_value'],
														) );
													}
													$afb2b_role_data_for_template_design[] = array(
														'min_qty'                   => $rule_cus_price['min_qty'],
														'max_qty'                   => $rule_cus_price['max_qty'],
														'discounted_price'          => $newprice,
														'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
														'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
													);

													//Aelia currency switcher compatibility
													$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

												} elseif ('fixed_increase' == $rule_cus_price['discount_type'] ) {

													if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

														$newprice = 0;
														$newprice = $newprice + $rule_cus_price['discount_value'];
													} else {

														$newprice = $pro_price + $rule_cus_price['discount_value'];
													}

													if ( 'incl' == $this->get_tax_price_display_mode() ) {
														$newprice = wc_get_price_including_tax( $product, array(
															'qty'   => 1,
															'price' => $newprice,
														) );
													} else {
														$newprice = wc_get_price_excluding_tax( $product, array(
															'qty'   => 1,
															'price' => $newprice,
														) );
													}
													$afb2b_role_data_for_template_design[] = array(
														'min_qty'                   => $rule_cus_price['min_qty'],
														'max_qty'                   => $rule_cus_price['max_qty'],
														'discounted_price'          => $newprice,
														'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
														'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
													);

													//Aelia currency switcher compatibility
													$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


												} elseif ('fixed_decrease' == $rule_cus_price['discount_type'] ) {

													if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

														$newprice = 0;
													} else {

														$newprice = $pro_price - $rule_cus_price['discount_value'];
													}

													if ( 'incl' == $this->get_tax_price_display_mode() ) {
														$newprice = wc_get_price_including_tax( $product, array(
															'qty'   => 1,
															'price' => $newprice,
														) );
													} else {
														$newprice = wc_get_price_excluding_tax( $product, array(
															'qty'   => 1,
															'price' => $newprice,
														) );
													}
													$afb2b_role_data_for_template_design[] = array(
														'min_qty'                   => $rule_cus_price['min_qty'],
														'max_qty'                   => $rule_cus_price['max_qty'],
														'discounted_price'          => $newprice,
														'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
														'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
													);

													//Aelia currency switcher compatibility
													$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

												} elseif ('percentage_decrease' == $rule_cus_price['discount_type'] ) {

													if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

														$newprice = 0;

													} else {

														$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

														$newprice = $pro_price - $percent_price;
													}

													   
													if ( 'incl' == $this->get_tax_price_display_mode() ) {
														$newprice = wc_get_price_including_tax( $product, array(
															'qty'   => 1,
															'price' => $newprice,
														) );
													} else {
														$newprice = wc_get_price_excluding_tax( $product, array(
															'qty'   => 1,
															'price' => $newprice,
														) );
													}
													$afb2b_role_data_for_template_design[] = array(
														'min_qty'                   => $rule_cus_price['min_qty'],
														'max_qty'                   => $rule_cus_price['max_qty'],
														'discounted_price'          => $newprice,
														'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
														'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
													);

													//Aelia currency switcher compatibility
													$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


												} elseif ('percentage_increase' == $rule_cus_price['discount_type'] ) {

													if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

														$newprice = 0;

													} else {

														$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

														$newprice = $pro_price + $percent_price;
													}

													
													if ( 'incl' == $this->get_tax_price_display_mode() ) {
														$newprice = wc_get_price_including_tax( $product, array(
															'qty'   => 1,
															'price' => $newprice,
														) );
													} else {
														$newprice = wc_get_price_excluding_tax( $product, array(
															'qty'   => 1,
															'price' => $newprice,
														) );
													}
													$afb2b_role_data_for_template_design[] = array(
														'min_qty'                   => $rule_cus_price['min_qty'],
														'max_qty'                   => $rule_cus_price['max_qty'],
														'discounted_price'          => $newprice,
														'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
														'replace_original_price'    => isset($rule_cus_price['replace_orignal_price']) && 'yes' == $rule_cus_price['replace_orignal_price']?'yes':'no',
													);

													//Aelia currency switcher compatibility
													$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

												}
											}
										}
									}
								}

								// Rule Role Based Pricing
								// chcek if there is customer specific pricing then role base pricing will not work.
								if (! $customer_discount1 ) {

									if (! empty($rule_role_base_price) ) {


										foreach ( $rule_role_base_price as $rule_role_price ) {

											if (current( $role ) == $rule_role_price['user_role'] ) {

												if ('' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) {


													$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

													//Fixed Price
													if ('fixed_price' == $rule_role_price['discount_type'] ) {

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_role_price['discount_value'],
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_role_price['discount_value'],
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

													} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {

														if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;
															$newprice = $newprice + $rule_role_price['discount_value'];
														} else {

															$newprice = $pro_price + $rule_role_price['discount_value'];
														}


														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

													} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

														if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;
														} else {

															$newprice = $pro_price - $rule_role_price['discount_value'];
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

													} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {

														if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

															$newprice = $pro_price - $percent_price;
														}
														
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


													} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {

														if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

															$newprice = $pro_price + $percent_price;
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);
														
														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

													}
												}
											}
										}
									}
								}
							}
						}
					}
				}

			} elseif (!is_user_logged_in() ) {

				// User is not logged in
				if (!empty($price_for_discount['guest']) && 'sale' == $price_for_discount['guest'] && !empty(get_post_meta($variation->get_id(), '_sale_price', true))) {

					$pro_price = get_post_meta($variation->get_id(), '_sale_price', true);

				} elseif (!empty($price_for_discount['guest']) && 'regular' === $price_for_discount['guest'] && !empty(get_post_meta($variation->get_id(), '_regular_price', true))) {

					$pro_price = get_post_meta($variation->get_id(), '_regular_price', true);

				} else {

					$pro_price = get_post_meta($variation->get_id(), '_price', true);
				}

				$pro_price_catering_tax = 'incl' == $this->get_tax_price_display_mode()?wc_get_price_including_tax( $product, array(
					'qty'   => 1,
					'price' => $pro_price,
				) ):wc_get_price_excluding_tax( $product, array(
					'qty'   => 1,
					'price' => $pro_price,
				) );

				// Role Based Pricing for guest
				if (true ) {

					// get role base price for guest
					$role_base_price = get_post_meta($variation->get_id(), '_role_base_price', true);

					if (! empty($role_base_price) ) {

						foreach ( $role_base_price as $role_price ) {

							if (isset($role_price['user_role']) && 'guest' == $role_price['user_role'] ) {

								if ('' != $role_price['discount_value'] || 0 != $role_price['discount_value'] ) {

										$role_discount = true;

										$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

										//Fixed Price
									if ('fixed_price' == $role_price['discount_type'] ) {

										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $role_price['discount_value'],
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $role_price['discount_value'],
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									} elseif ('fixed_increase' == $role_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
											$newprice = $newprice + $role_price['discount_value'];
										} else {

											$newprice = $pro_price + $role_price['discount_value'];
										}

											
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									} elseif ('fixed_decrease' == $role_price['discount_type'] ) {


										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$newprice = $pro_price - $role_price['discount_value'];
										}
	
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


									} elseif ('percentage_decrease' == $role_price['discount_type'] ) {

										if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;
										}


											
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


									} elseif ('percentage_increase' == $role_price['discount_type'] ) {


										if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}


											
										if ( 'incl' == $this->get_tax_price_display_mode() ) {
											$newprice = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										} else {
											$newprice = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $newprice,
											) );
										}
										$afb2b_role_data_for_template_design[] = array(
											'min_qty'      => $role_price['min_qty'],
											'max_qty'      => $role_price['max_qty'],
											'discounted_price' => $newprice,
											'saved_amount' => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
											'replace_original_price' => isset($role_price['replace_orignal_price']) && 'yes' == $role_price['replace_orignal_price']?'yes':'no',
										);

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

									}
								}
							}
						}
					}

					// Rules - guest users
					if (false == $role_discount  ) {

						if (empty($this->allfetchedrules) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if (! empty($all_rules) ) {

							foreach ( $all_rules as $rule ) {

								$istrue = false;
									

								$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if ('yes' == $applied_on_all_products ) {
									$istrue = true;
								} elseif (! empty($products) && ( in_array($variation->get_id(), $products) || in_array($variation->get_parent_id(), $products) ) ) {
									$istrue = true;
								}


								if (!empty($categories)) {
									foreach ( $categories as $cat ) {

										if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $variation->get_id() )  ||  has_term( $cat, 'product_cat', $variation->get_parent_id() ) ) ) {

											$istrue = true;
										} 
									}
								}


								if (!empty($rbp_slected_brands)) {
									foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

											
										if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $variation->get_id() )  ||  has_term( $rbp_brand_slect, 'product_brand', $variation->get_parent_id() ) ) ) {

											$istrue = true;
										} 

									}
								}

									


								if ($istrue ) {


									// get rule role base price for guest
									$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);


									if (! empty($rule_role_base_price) ) {


										foreach ( $rule_role_base_price as $rule_role_price ) {

											if ('guest' == $rule_role_price['user_role'] ) {

												if ('' != $rule_role_price['discount_value'] || 0 != $rule_role_price['discount_value'] ) {


													$csp_range_msg = __(get_option('csp_range_msg'), 'addify_b2b');

													//Fixed Price
													if ('fixed_price' == $rule_role_price['discount_type'] ) {

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_role_price['discount_value'],
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $rule_role_price['discount_value'],
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


													} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {

														if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;
															$newprice = $newprice + $rule_role_price['discount_value'];
														} else {

															$newprice = $pro_price + $rule_role_price['discount_value'];
														}


														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


													} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

														if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;
														} else {
															$newprice = $pro_price - $rule_role_price['discount_value'];
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);                                                          

													} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {


														if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {
															$newprice = 0;
														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

															$newprice = $pro_price - $percent_price;
														}
																
														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

													} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {


														if (( empty($pro_price) ) || ( !empty($pro_price) && 0 == $pro_price )) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

															$newprice = $pro_price + $percent_price;
														}

														if ( 'incl' == $this->get_tax_price_display_mode() ) {
															$newprice = wc_get_price_including_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														} else {
															$newprice = wc_get_price_excluding_tax( $product, array(
																'qty'   => 1,
																'price' => $newprice,
															) );
														}
														$afb2b_role_data_for_template_design[] = array(
															'min_qty'                   => $rule_role_price['min_qty'],
															'max_qty'                   => $rule_role_price['max_qty'],
															'discounted_price'          => $newprice,
															'saved_amount'              => ( $pro_price_catering_tax - $newprice )>0?$pro_price_catering_tax - $newprice:0,
															'replace_original_price'    => isset($rule_role_price['replace_orignal_price']) && 'yes' == $rule_role_price['replace_orignal_price']?'yes':'no',
														);

														//Aelia currency switcher compatibility
														$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);                                                          

													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				
			}

			// $data['price_html'] .= '<span class="4xcb"> ' . $msg_data . '</span>';
			
			if (!empty($afb2b_role_data_for_template_design)) {
				ob_start();
				include AFB2B_PLUGIN_DIR . 'templates/pricing_design/afb2b_role_pricing_design_template.php';
				$template_data = ob_get_clean();
				
				$data['price_html'] .= '<span class="4xcb"> ' . $template_data . '</span>';
			
			}

			return $data;
		}



		public function af_csp_recalculate_price( $cart_object ) {

			// Avoiding hook repetition (when using price calculations for example)
			if (did_action('woocommerce_before_calculate_totals') >= 2 ) {
				return;
			}

			$user           = wp_get_current_user();
			$role           = ( array ) $user->roles;
			$quantity       = 0;
			$rule_cus_price = '';

			$price_for_discount = get_option('afb2b_discount_price');


			$active_currency = get_woocommerce_currency();
			$base_currency   = get_option( 'woocommerce_currency' );


			if (is_user_logged_in() ) {

				foreach ( $cart_object->get_cart() as $key => $value ) {

					$customer_discount  = false;
					$role_discount      = false;
					$customer_discount1 = false;
					$role_discount1     = false;

					$quantity += $value['quantity'];

					if (0 != $value['variation_id']) {

						$product_id = $value['variation_id'];
						$parent_id  = $value['product_id'];

					} else {

						$product_id = $value['product_id'];
						$parent_id  = 0;

					}

					$first_role = current($user->roles);

					if (!empty($price_for_discount[ $first_role ]) && 'sale' == $price_for_discount[ $first_role ] && !empty($value['data']->get_sale_price())) {

						$pro_price = $value['data']->get_sale_price();

					} elseif (!empty($price_for_discount[ $first_role ]) && 'regular' === $price_for_discount[ $first_role ] && !empty($value['data']->get_regular_price())) {

						$pro_price = $value['data']->get_regular_price();

					} else {

						$pro_price = $value['data']->get_price();
					}
					
					// get customer specific price
					$cus_base_price = get_post_meta($product_id, '_cus_base_price', true);
					// get role base price
					$role_base_price = get_post_meta($product_id, '_role_base_price', true);

					//Customer pricing

					if (! empty($cus_base_price) ) {

						foreach ( $cus_base_price as $cus_price ) {

							if (isset($cus_price['customer_name']) && $user->ID == $cus_price['customer_name'] ) {

								if (( $value['quantity'] >= $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] ) 
									|| ( $value['quantity'] >= $cus_price['min_qty'] && '' == $cus_price['max_qty'] )
									|| ( $value['quantity'] >= $cus_price['min_qty'] && 0 == $cus_price['max_qty'] ) 
									|| ( '' == $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] ) 
									|| ( 0 == $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
								) {


									if ('fixed_price' == $cus_price['discount_type'] ) {

										//Aelia currency switcher compatibility
										$converted_amount = apply_filters('wc_aelia_cs_convert', $cus_price['discount_value'], $base_currency, $active_currency);

										$value['data']->set_price($converted_amount);
										$customer_discount = true;

									} elseif ('fixed_increase' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
											$newprice = $newprice + $cus_price['discount_value'];
										} else {

											$newprice = $pro_price + $cus_price['discount_value'];
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										
										$value['data']->set_price($newprice);
										$customer_discount = true;

									} elseif ('fixed_decrease' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$newprice = $pro_price - $cus_price['discount_value'];

											if (0 > $newprice) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										
										$value['data']->set_price($newprice);
										$customer_discount = true;

									} elseif ('percentage_decrease' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;

											if (0 > $newprice) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$value['data']->set_price($newprice);
										$customer_discount = true;

									} elseif ('percentage_increase' == $cus_price['discount_type'] ) {


										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$value['data']->set_price($newprice);
										$customer_discount = true;

									} else {

										$customer_discount = false;
									}
								}
							}
							
						}
					} else {

						$customer_discount = false;
					}

					// Role Based Pricing
					// chcek if there is customer specific pricing then role base pricing will not work.
					if (! $customer_discount ) {

						if (! empty($role_base_price) ) {

							foreach ( $role_base_price as $role_price ) {

								if (isset($role_price['user_role']) && current( $role ) == $role_price['user_role'] ) {

									if (( $value['quantity'] >= $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] ) 
										|| ( $value['quantity'] >= $role_price['min_qty'] && '' == $role_price['max_qty'] )
										|| ( $value['quantity'] >= $role_price['min_qty'] && 0 == $role_price['max_qty'] ) 
										|| ( '' == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] ) 
										|| ( 0 == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									) {

										if ('fixed_price' == $role_price['discount_type'] ) {

											// Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $role_price['discount_value'], $base_currency, $active_currency);

											$value['data']->set_price($newprice);
											$role_discount = true;

										} elseif ('fixed_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												$newprice = $newprice + $role_price['discount_value'];
											} else {

												$newprice = $pro_price + $role_price['discount_value'];
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											
											$value['data']->set_price($newprice);
											$role_discount = true;

										} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												
											} else {

												$newprice = $pro_price - $role_price['discount_value'];
												if (0 > $newprice) {

													$newprice = 0;

												} else {

													$newprice = $newprice;

												}
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											
											$value['data']->set_price($newprice);
											$role_discount = true;

										} elseif ('percentage_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												
											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price - $percent_price;

												if (0 > $newprice) {

													$newprice = 0;

												} else {

													$newprice = $newprice;

												}
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$value['data']->set_price($newprice);
											$role_discount = true;

										} elseif ('percentage_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												
											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price + $percent_price;
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
											
											$value['data']->set_price($newprice);
											$role_discount = true;

										} else {

											$role_discount = false;
										}
									}
								}
							}
						} else {

							$role_discount = false;
						}
					}


					//Rules
					if (false == $customer_discount && false == $role_discount ) {
					

						if (empty($this->allfetchedrules) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if (! empty($all_rules) ) {

							$rule_check = false;

							foreach ( $all_rules as $rule ) {

								$istrue = false;
							

								$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if (!$rule_check) {

									if ('yes' == $applied_on_all_products ) {
										$istrue = true;
										//$rule_check = true;
									} elseif (! empty($products) && ( in_array($product_id, $products) || in_array($parent_id, $products) ) ) {
										$istrue = true;
										//$rule_check = true;
									}


									if (!empty($categories)) {
										foreach ( $categories as $cat ) {

											if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product_id )  ||  has_term( $cat, 'product_cat', $parent_id ) ) ) {

												$istrue = true;
												//$rule_check = true;
											} 
										}
									}


									if (!empty($rbp_slected_brands)) {
										foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

											

											if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

												$istrue = true;
												
											} 
										}
									}



									

									if ($istrue ) {

										// get Rule customer specifc price
										$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

										// get role base price
										$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);



										if (! empty($rule_cus_base_price) ) {

											foreach ( $rule_cus_base_price as $rule_cus_price ) {

												if (isset($rule_cus_price['customer_name']) && $user->ID == $rule_cus_price['customer_name'] ) {

													if (( $value['quantity'] >= $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] ) 
														|| ( $value['quantity'] >= $rule_cus_price['min_qty'] && '' == $rule_cus_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_cus_price['min_qty'] && 0 == $rule_cus_price['max_qty'] ) 
														|| ( '' == $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] ) 
														|| ( 0 == $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
													) {

														$rule_check = true;

														if ('fixed_price' == $rule_cus_price['discount_type'] ) {

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $rule_cus_price['discount_value'], $base_currency, $active_currency);

															$value['data']->set_price($newprice);
															$customer_discount1 = true;
															

														} elseif ('fixed_increase' == $rule_cus_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																$newprice = $newprice + $rule_cus_price['discount_value'];
																
															} else {

																$newprice = $pro_price + $rule_cus_price['discount_value'];
															}

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															
															$value['data']->set_price($newprice);
															$customer_discount1 = true;
															

														} elseif ('fixed_decrease' == $rule_cus_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																
																
															} else {

																$newprice = $pro_price - $rule_cus_price['discount_value'];
																if (0 > $newprice) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															
															$value['data']->set_price($newprice);
															$customer_discount1 = true;
															

														} elseif ('percentage_decrease' == $rule_cus_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																
																
															} else {

																$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

																$newprice = $pro_price - $percent_price;

																if (0 > $newprice) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$value['data']->set_price($newprice);
															$customer_discount1 = true;
															

														} elseif ('percentage_increase' == $rule_cus_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																
																
															} else {

																$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

																$newprice = $pro_price + $percent_price;
															}

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$value['data']->set_price($newprice);
															$customer_discount1 = true;
															

														} else {

															$customer_discount1 = false;
														}
													}
												}
											}
										} else {

											$customer_discount1 = false;
										}

										// Rule Role Based Pricing
										// chcek if there is customer specific pricing then role base pricing will not work.
										if (! $customer_discount1 ) {

											
											if (! empty($rule_role_base_price) ) {

												foreach ( $rule_role_base_price as $rule_role_price ) {

													if (isset($rule_role_price['user_role']) && current( $role ) == $rule_role_price['user_role'] ) {

														if (( $value['quantity'] >= $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] ) 
															|| ( $value['quantity'] >= $rule_role_price['min_qty'] && '' == $rule_role_price['max_qty'] )
															|| ( $value['quantity'] >= $rule_role_price['min_qty'] && 0 == $rule_role_price['max_qty'] ) 
															|| ( '' == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] ) 
															|| ( 0 == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														) {

															$rule_check = true;

															if ('fixed_price' == $rule_role_price['discount_type'] ) {

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency);

																$value['data']->set_price($newprice);
																

															} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	$newprice = $newprice + $rule_role_price['discount_value'];
																	
																	
																} else {

																	$newprice = $pro_price + $rule_role_price['discount_value'];
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															
																$value['data']->set_price($newprice);
																

															} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	
																	
																	
																} else {

																	$newprice = $pro_price - $rule_role_price['discount_value'];

																	if (0 > $newprice) {

																		$newprice = 0;

																	} else {

																		$newprice = $newprice;

																	}
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																
																$value['data']->set_price($newprice);
																

															} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	
																	
																	
																} else {

																	$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																	$newprice = $pro_price - $percent_price;

																	if (0 > $newprice) {

																		$newprice = 0;

																	} else {

																		$newprice = $newprice;

																	}
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																$value['data']->set_price($newprice);
																

															} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	
																	
																	
																} else {

																	$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																	$newprice = $pro_price + $percent_price;
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																$value['data']->set_price($newprice);
																

															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}

			} elseif (!is_user_logged_in() ) {

				//Guest user
				// User is not logged in
				

				foreach ( $cart_object->get_cart() as $key => $value ) {

					$customer_discount  = false;
					$role_discount      = false;
					$customer_discount1 = false;
					$role_discount1     = false;

					$quantity += $value['quantity'];

					if (0 != $value['variation_id']) {

						$product_id = $value['variation_id'];
						$parent_id  = $value['product_id'];

					} else {

						$product_id = $value['product_id'];
						$parent_id  = 0;

					}

					if (!empty($price_for_discount['guest']) && 'sale' == $price_for_discount['guest'] && !empty($value['data']->get_sale_price())) {

						$pro_price = $value['data']->get_sale_price();

					} elseif (!empty($price_for_discount['guest']) && 'regular' === $price_for_discount['guest'] && !empty($value['data']->get_regular_price())) {

						$pro_price = $value['data']->get_regular_price();

					} else {

						$pro_price = $value['data']->get_price();
					}


					// Role Based Pricing for guest
					if (true ) {

						// get role base price for guest
						$role_base_price = get_post_meta($product_id, '_role_base_price', true);
							
						if (! empty($role_base_price) ) {

							foreach ( $role_base_price as $role_price ) {

								if (isset($role_price['user_role']) && 'guest' == $role_price['user_role'] ) {

									if (( $value['quantity'] >= $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] ) 
										|| ( $value['quantity'] >= $role_price['min_qty'] && '' == $role_price['max_qty'] )
										|| ( $value['quantity'] >= $role_price['min_qty'] && 0 == $role_price['max_qty'] ) 
										|| ( '' == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] ) 
										|| ( 0 == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									) {


										if ('fixed_price' == $role_price['discount_type'] ) {

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $role_price['discount_value'], $base_currency, $active_currency);

											$value['data']->set_price($newprice);
											$role_discount = true;

										} elseif ('fixed_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												$newprice = $newprice + $role_price['discount_value'];
																										
											} else {

												$newprice = $pro_price + $role_price['discount_value'];
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

												
											$value['data']->set_price($newprice);
											$role_discount = true;

										} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
													
																										
											} else {

												$newprice = $pro_price - $role_price['discount_value'];

												if (0 > $newprice) {

													$newprice = 0;

												} else {

													$newprice = $newprice;

												}
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

												
											$value['data']->set_price($newprice);
											$role_discount = true;

										} elseif ('percentage_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
													
																										
											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price - $percent_price;

												if (0 > $newprice) {

													$newprice = 0;

												} else {

													$newprice = $newprice;

												}
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$value['data']->set_price($newprice);
											$role_discount = true;

										} elseif ('percentage_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
													
																										
											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price + $percent_price;
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$value['data']->set_price($newprice);
											$role_discount = true;

										} else {

											$role_discount = false;
										}
									}
								}
							}
						} else {

							$role_discount = false;
						}

						// Rules - guest users
						if (false == $role_discount  ) {

							if (empty($this->allfetchedrules) ) {

									echo '';

							} else {

								$all_rules = $this->allfetchedrules;

							}

							if (! empty($all_rules) ) {

								$rule_check = false;

								foreach ( $all_rules as $rule ) {

									$istrue = false;
										

									$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
									$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
									$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
									$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

									if (!$rule_check) {

										if ('yes' == $applied_on_all_products ) {
											$istrue = true;
											//$rule_check = true;
										} elseif (! empty($products) && ( in_array($product_id, $products) || in_array($parent_id, $products) ) ) {
											$istrue = true;
											//$rule_check = true;
										}


										if (!empty($categories)) {
											foreach ( $categories as $cat ) {

												if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product_id )  || has_term( $cat, 'product_cat', $parent_id ) ) ) {

													$istrue = true;
													//$rule_check = true;
												} 
											}
										}


										if (!empty($rbp_slected_brands)) {
											foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

												if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

													$istrue = true;
														
												} 


											}
										}


											

										if ($istrue ) {

											// get rule role base price for guest
											$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

											if (! empty($rule_role_base_price) ) {

												foreach ( $rule_role_base_price as $rule_role_price ) {

													if (isset($rule_role_price['user_role']) && 'guest' == $rule_role_price['user_role'] ) {

														if (( $value['quantity'] >= $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] ) 
															|| ( $value['quantity'] >= $rule_role_price['min_qty'] && '' == $rule_role_price['max_qty'] )
															|| ( $value['quantity'] >= $rule_role_price['min_qty'] && 0 == $rule_role_price['max_qty'] ) 
															|| ( '' == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] ) 
															|| ( 0 == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														) {

															$rule_check = true;

															if ('fixed_price' == $rule_role_price['discount_type'] ) {

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency);

																$value['data']->set_price($newprice);
																	

															} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	$newprice = $newprice + $rule_role_price['discount_value'];
																		
																															
																} else {

																	$newprice = $pro_price + $rule_role_price['discount_value'];
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																	
																$value['data']->set_price($newprice);
																	

															} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																		
																		
																															
																} else {

																	$newprice = $pro_price - $rule_role_price['discount_value'];

																	if (0 > $newprice) {

																		$newprice = 0;

																	} else {

																		$newprice = $newprice;

																	}
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																	
																$value['data']->set_price($newprice);
																	

															} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																		
																		
																															
																} else {

																	$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																	$newprice = $pro_price - $percent_price;

																	if (0 > $newprice) {

																		$newprice = 0;

																	} else {

																		$newprice = $newprice;

																	}
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency); 

																$value['data']->set_price($newprice);
																	

															} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																		
																		
																															
																} else {

																	$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																	$newprice = $pro_price + $percent_price;
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																$value['data']->set_price($newprice);

															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
				
			}
		}


		public function af_csp_woocommerce_cart_item_price_filter( $price, $cart_item, $cart_item_key ) {

			$newprice = 0;
			$product  = isset( $cart_item['data'] ) ? $cart_item['data'] : null;

			$active_currency = get_woocommerce_currency();
			$base_currency   = get_option( 'woocommerce_currency' );
			
			if (! is_cart() ) {

				$user               = wp_get_current_user();
				$role               = ( array ) $user->roles;
				$quantity           = 0;
				$customer_discount  = false;
				$role_discount      = false;
				$customer_discount1 = false;
				$role_discount1     = false;
				$parent_id          = 0;

				$price_for_discount = get_option('afb2b_discount_price');

				if (0 != $cart_item['variation_id']) {

					$product_id = $cart_item['variation_id'];
					$parent_id  = $cart_item['product_id'];

				} else {

					$product_id = $cart_item['product_id'];
					$parent_id  = 0;

				}

				$quantity += $cart_item['quantity'];

				if (is_user_logged_in() ) {

					// get customer specifc price
					$cus_base_price = get_post_meta($product_id, '_cus_base_price', true);

					// get role base price
					$role_base_price = get_post_meta($product_id, '_role_base_price', true);
			
					$first_role = current($user->roles);


					if (!empty($price_for_discount[ $first_role ]) && 'sale' == $price_for_discount[ $first_role ] && !empty($cart_item['data']->get_sale_price())) {

						$pro_price = $cart_item['data']->get_sale_price('edit');

					} elseif (!empty($price_for_discount[ $first_role ]) && 'regular' == $price_for_discount[ $first_role ] && !empty($cart_item['data']->get_regular_price())) {

						$pro_price = $cart_item['data']->get_regular_price('edit');

					} elseif ( !empty( $product->get_sale_price() ) ) {


							$pro_price = $cart_item['data']->get_sale_price('edit');
					} else {
						$pro_price = $cart_item['data']->get_regular_price('edit');
					}

					//Customer pricing

					if (! empty($cus_base_price) ) {

						foreach ( $cus_base_price as $cus_price ) {

							if (isset($cus_price['customer_name']) && $user->ID == $cus_price['customer_name'] ) {

								if (( $cart_item['quantity'] >= $cus_price['min_qty'] && $cart_item['quantity'] <= $cus_price['max_qty'] ) 
									|| ( $cart_item['quantity'] >= $cus_price['min_qty'] && '' == $cus_price['max_qty'] )
									|| ( $cart_item['quantity'] >= $cus_price['min_qty'] && 0 == $cus_price['max_qty'] ) 
									|| ( '' == $cus_price['min_qty'] && $cart_item['quantity'] <= $cus_price['max_qty'] ) 
									|| ( 0 == $cus_price['min_qty'] && $cart_item['quantity'] <= $cus_price['max_qty'] )
								) {


									if ('fixed_price' == $cus_price['discount_type'] ) {

										if ( 'incl' === $this->get_tax_price_display_mode() ) {
											$product_priceFix = wc_get_price_including_tax( $product, array(
												'qty'   => 1,
												'price' => $cus_price['discount_value'],
											) );
										} else {
											$product_priceFix = wc_get_price_excluding_tax( $product, array(
												'qty'   => 1,
												'price' => $cus_price['discount_value'],
											) );
										}

										//Aelia currency switcher compatibility
										$product_priceFix = apply_filters('wc_aelia_cs_convert', $product_priceFix, $base_currency, $active_currency);

										$price             = wc_price($product_priceFix);
										$customer_discount = true;

									} elseif ('fixed_increase' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
											$newprice = $newprice + $cus_price['discount_value'];
										} else {

											$newprice = $pro_price + $cus_price['discount_value'];
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
										
										$price             = wc_price($newprice);
										$customer_discount = true;

									} elseif ('fixed_decrease' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$newprice = $pro_price - $cus_price['discount_value'];
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										
										$price             = wc_price($newprice);
										$customer_discount = true;

									} elseif ('percentage_decrease' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


										$price             = wc_price($newprice);
										$customer_discount = true;

									} elseif ('percentage_increase' == $cus_price['discount_type'] ) {

										if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

											$newprice = 0;
										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										//Aelia currency switcher compatibility
										$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

										$price             = wc_price($newprice);
										$customer_discount = true;

									}
								}
							}
						}
					}

					// Role Based Pricing
					// chcek if there is customer specific pricing then role base pricing will not work.
					if (! $customer_discount ) {

						if (! empty($role_base_price) ) {

							foreach ( $role_base_price as $role_price ) {

								if (isset($role_price['user_role']) && current( $role ) == $role_price['user_role'] ) {

									if (( $cart_item['quantity'] >= $role_price['min_qty'] && $cart_item['quantity'] <= $role_price['max_qty'] ) 
										|| ( $cart_item['quantity'] >= $role_price['min_qty'] && '' == $role_price['max_qty'] )
										|| ( $cart_item['quantity'] >= $role_price['min_qty'] && 0 == $role_price['max_qty'] ) 
										|| ( '' == $role_price['min_qty'] && $cart_item['quantity'] <= $role_price['max_qty'] ) 
										|| ( 0 == $role_price['min_qty'] && $cart_item['quantity'] <= $role_price['max_qty'] )
									) {


										if ('fixed_price' == $role_price['discount_type'] ) {

											if ( 'incl' === $this->get_tax_price_display_mode() ) {
												$product_priceFix = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $role_price['discount_value'],
												) );
											} else {
												$product_priceFix = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $role_price['discount_value'],
												) );
											}

											//Aelia currency switcher compatibility
											$product_priceFix = apply_filters('wc_aelia_cs_convert', $product_priceFix, $base_currency, $active_currency);

											$price         = wc_price($product_priceFix);
											$role_discount = true;

										} elseif ('fixed_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												$newprice = $newprice + $role_price['discount_value'];
											} else {

												$newprice = $pro_price + $role_price['discount_value'];
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$price         = wc_price($newprice);
											$role_discount = true;

										} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
											} else {

												$newprice = $pro_price - $role_price['discount_value'];
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											
											$price         = wc_price($newprice);
											$role_discount = true;

										} elseif ('percentage_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price - $percent_price;
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


											$price         = wc_price($newprice);
											$role_discount = true;

										} elseif ('percentage_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price + $percent_price;
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

											$price         = wc_price($newprice);
											$role_discount = true;

										}
									}
								}
							}
						}
					}

					//Rules
					if (false == $customer_discount && false == $role_discount ) {

						if (empty($this->allfetchedrules) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if (! empty($all_rules) ) {

							$rule_check = false;

							foreach ( $all_rules as $rule ) {

								$istrue = false;
								

								$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
								$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
								$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if (!$rule_check) {

									if ('yes' == $applied_on_all_products ) {
										$istrue = true;
										//$rule_check = true;
									} elseif (! empty($products) && ( in_array($product_id, $products) || in_array($parent_id, $products) ) ) {
										$istrue = true;
										//  $rule_check = true;
									}


									if (!empty($categories)) {
										foreach ( $categories as $cat ) {

											if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product_id )  ||  has_term( $cat, 'product_cat', $parent_id ) ) ) {

												$istrue = true;
												//$rule_check = true;
											} 
										}
									}


									if (!empty($rbp_slected_brands)) {
										foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

											

											if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

												$istrue = true;
												
											} 
										}
									}


									if ($istrue ) {

										// get Rule customer specifc price
										$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

										// get role base price
										$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

										if (! empty($rule_cus_base_price) ) {

											foreach ( $rule_cus_base_price as $rule_cus_price ) {

												if (isset($rule_cus_price['customer_name']) && $user->ID == $rule_cus_price['customer_name'] ) {

													if (( $cart_item['quantity'] >= $rule_cus_price['min_qty'] && $cart_item['quantity'] <= $rule_cus_price['max_qty'] ) 
														|| ( $cart_item['quantity'] >= $rule_cus_price['min_qty'] && '' == $rule_cus_price['max_qty'] )
														|| ( $cart_item['quantity'] >= $rule_cus_price['min_qty'] && 0 == $rule_cus_price['max_qty'] ) 
														|| ( '' == $rule_cus_price['min_qty'] && $cart_item['quantity'] <= $rule_cus_price['max_qty'] ) 
														|| ( 0 == $rule_cus_price['min_qty'] && $cart_item['quantity'] <= $rule_cus_price['max_qty'] )
													) {

															$rule_check = true;

														if ('fixed_price' == $rule_cus_price['discount_type'] ) {

															if ( 'incl' === $this->get_tax_price_display_mode() ) {
																$product_priceFix = wc_get_price_including_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_cus_price['discount_value'],
																) );
															} else {
																$product_priceFix = wc_get_price_excluding_tax( $product, array(
																	'qty'   => 1,
																	'price' => $rule_cus_price['discount_value'],
																) );
															}

															//Aelia currency switcher compatibility
															$product_priceFix = apply_filters('wc_aelia_cs_convert', $product_priceFix, $base_currency, $active_currency);

															$price              = wc_price($product_priceFix);
															$customer_discount1 = true;

														} elseif ('fixed_increase' == $rule_cus_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																$newprice = $newprice + $rule_cus_price['discount_value'];
															} else {

																$newprice = $pro_price + $rule_cus_price['discount_value'];
															}

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															
															$price              = wc_price($newprice);
															$customer_discount1 = true;

														} elseif ('fixed_decrease' == $rule_cus_price['discount_type'] ) {

															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																
															} else {

																$newprice = $pro_price - $rule_cus_price['discount_value'];
															}

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															
															$price              = wc_price($newprice);
															$customer_discount1 = true;

														} elseif ('percentage_decrease' == $rule_cus_price['discount_type'] ) {


															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																
															} else {

																$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

																$newprice = $pro_price - $percent_price;
															}

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

															$price              = wc_price($newprice);
															$customer_discount1 = true;

														} elseif ('percentage_increase' == $rule_cus_price['discount_type'] ) {


															if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																$newprice = 0;
																
															} else {

																$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

																$newprice = $pro_price + $percent_price;
															}

															//Aelia currency switcher compatibility
															$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency); 

															$price              = wc_price($newprice);
															$customer_discount1 = true;

														}
													}
												}
											}
										}

										// Rule Role Based Pricing
										// chcek if there is customer specific pricing then role base pricing will not work.
										if (! $customer_discount1 ) {

											if (! empty($rule_role_base_price) ) {

												foreach ( $rule_role_base_price as $rule_role_price ) {

													if (isset($rule_role_price['user_role']) && current( $role ) == $rule_role_price['user_role'] ) {

														if (( $cart_item['quantity'] >= $rule_role_price['min_qty'] && $cart_item['quantity'] <= $rule_role_price['max_qty'] ) 
															|| ( $cart_item['quantity'] >= $rule_role_price['min_qty'] && '' == $rule_role_price['max_qty'] )
															|| ( $cart_item['quantity'] >= $rule_role_price['min_qty'] && 0 == $rule_role_price['max_qty'] ) 
															|| ( '' == $rule_role_price['min_qty'] && $cart_item['quantity'] <= $rule_role_price['max_qty'] ) 
															|| ( 0 == $rule_role_price['min_qty'] && $cart_item['quantity'] <= $rule_role_price['max_qty'] )
														) {

															$rule_check = true; 

															if ('fixed_price' == $rule_role_price['discount_type'] ) {

																if ( 'incl' === $this->get_tax_price_display_mode() ) {
																	$product_priceFix = wc_get_price_including_tax( $product, array(
																		'qty'   => 1,
																		'price' => $rule_role_price['discount_value'],
																	) );
																} else {
																	$product_priceFix = wc_get_price_excluding_tax( $product, array(
																		'qty'   => 1,
																		'price' => $rule_role_price['discount_value'],
																	) );
																}

																//Aelia currency switcher compatibility
																$product_priceFix = apply_filters('wc_aelia_cs_convert', $product_priceFix, $base_currency, $active_currency);

																$price = wc_price($product_priceFix);

															} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {


																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	$newprice = $newprice + $rule_role_price['discount_value'];
																	
																} else {

																	$newprice = $pro_price + $rule_role_price['discount_value'];
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																$price = wc_price($newprice);

															} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	
																	
																} else {

																	$newprice = $pro_price- $rule_role_price['discount_value'];
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
																
																$price = wc_price($newprice);

															} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {

																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	
																	
																} else {

																	$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																	$newprice = $pro_price - $percent_price;
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																$price = wc_price($newprice);
																

															} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {


																if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																	$newprice = 0;
																	
																	
																} else {

																	$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																	$newprice = (float) $pro_price + (float) $percent_price;
																}

																//Aelia currency switcher compatibility
																$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																$price = wc_price($newprice);
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}

				} elseif (!is_user_logged_in() ) {

					//Guest user
					// User is not logged in
					

						// Role Based Pricing for guest
					if (true ) {

							// get role base price for guest
							$role_base_price = get_post_meta($product_id, '_role_base_price', true);

							  
						if (( !empty($price_for_discount['guest']) && 'sale' == $price_for_discount['guest'] ) && ( !empty($cart_item['data']->get_sale_price()) )) {

							$pro_price = $cart_item['data']->get_sale_price('edit');

						} elseif (( !empty($price_for_discount['guest']) && 'regular' == $price_for_discount['guest'] ) && ( !empty($cart_item['data']->get_regular_price()) )) {

							$pro_price = $cart_item['data']->get_regular_price('edit');

						} elseif ( !empty( $product->get_sale_price() ) ) {


								$pro_price = $cart_item['data']->get_sale_price('edit');
						} else {
							$pro_price = $cart_item['data']->get_regular_price('edit');
						}


						if (! empty($role_base_price) ) {

							foreach ( $role_base_price as $role_price ) {

								if (isset($role_price['user_role']) && 'guest' == $role_price['user_role'] ) {

									if (( $cart_item['quantity'] >= $role_price['min_qty'] && $cart_item['quantity'] <= $role_price['max_qty'] ) 
										|| ( $cart_item['quantity'] >= $role_price['min_qty'] && '' == $role_price['max_qty'] )
										|| ( $cart_item['quantity'] >= $role_price['min_qty'] && 0 == $role_price['max_qty'] ) 
										|| ( '' == $role_price['min_qty'] && $cart_item['quantity'] <= $role_price['max_qty'] ) 
										|| ( 0 == $role_price['min_qty'] && $cart_item['quantity'] <= $role_price['max_qty'] )
									) {


										if ('fixed_price' == $role_price['discount_type'] ) {

											if ( 'incl' === $this->get_tax_price_display_mode() ) {
												$product_priceFix = wc_get_price_including_tax( $product, array(
													'qty' => 1,
													'price' => $role_price['discount_value'],
												) );
											} else {
												$product_priceFix = wc_get_price_excluding_tax( $product, array(
													'qty' => 1,
													'price' => $role_price['discount_value'],
												) );
											}

											//Aelia currency switcher compatibility
											$product_priceFix = apply_filters('wc_aelia_cs_convert', $product_priceFix, $base_currency, $active_currency);

											$price         = wc_price($product_priceFix);
											$role_discount = true;

										} elseif ('fixed_increase' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
												$newprice = $newprice + $role_price['discount_value'];
													
													
											} else {

												$newprice = $pro_price + $role_price['discount_value'];
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


												
											$price         = wc_price($newprice);
											$role_discount = true;

										} elseif ('fixed_decrease' == $role_price['discount_type'] ) {

											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
													
													
													
											} else {

												$newprice = $pro_price - $role_price['discount_value'];
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);


												
											$price         = wc_price($newprice);
											$role_discount = true;

										} elseif ('percentage_decrease' == $role_price['discount_type'] ) {


											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
																										
											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price - $percent_price;
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
												

											$price         = wc_price($newprice);
											$role_discount = true;

										} elseif ('percentage_increase' == $role_price['discount_type'] ) {


											if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

												$newprice = 0;
																										
											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price + $percent_price;
											}

											//Aelia currency switcher compatibility
											$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
												

											$price         = wc_price($newprice);
											$role_discount = true;

										}

									}
								}
							}
						}

						// Rules - guest users
						if (false == $role_discount  ) {

							if (empty($this->allfetchedrules) ) {

								echo '';

							} else {

								$all_rules = $this->allfetchedrules;

							}

							if (! empty($all_rules) ) {

								$rule_check = false;

								foreach ( $all_rules as $rule ) {

									$istrue = false;
										

									$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
									$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
									$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
									$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

									if (!$rule_check) {

										if ('yes' == $applied_on_all_products ) {
											$istrue = true;
											//$rule_check = true;
										} elseif (! empty($products) && ( in_array($product_id, $products) || in_array($parent_id, $products) ) ) {
											$istrue = true;
											//$rule_check = true;
										}


										if (!empty($categories)) {
											foreach ( $categories as $cat ) {

												if ( !empty( $cat) && ( has_term( $cat, 'product_cat', $product_id )  ||  has_term( $cat, 'product_cat', $parent_id ) ) ) {

													$istrue = true;
													//$rule_check = true;
												} 
											}
										}


										if (!empty($rbp_slected_brands)) {
											foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

													

												if ( !empty( $rbp_brand_slect) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id )  ||  has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

													$istrue = true;
														
												} 

											}
										}
											


										if ($istrue ) {

											// get rule role base price for guest
											$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

											if (! $customer_discount1 ) {

												if (! empty($rule_role_base_price) ) {

													foreach ( $rule_role_base_price as $rule_role_price ) {

														if (isset($rule_role_price['user_role']) && 'guest' == $rule_role_price['user_role'] ) {

															if (( $cart_item['quantity'] >= $rule_role_price['min_qty'] && $cart_item['quantity'] <= $rule_role_price['max_qty'] ) 
																|| ( $cart_item['quantity'] >= $rule_role_price['min_qty'] && '' == $rule_role_price['max_qty'] )
																|| ( $cart_item['quantity'] >= $rule_role_price['min_qty'] && 0 == $rule_role_price['max_qty'] ) 
																|| ( '' == $rule_role_price['min_qty'] && $cart_item['quantity'] <= $rule_role_price['max_qty'] ) 
																|| ( 0 == $rule_role_price['min_qty'] && $cart_item['quantity'] <= $rule_role_price['max_qty'] )
															) {

																	$rule_check = true;

																if ('fixed_price' == $rule_role_price['discount_type'] ) {

																	if ( 'incl' === $this->get_tax_price_display_mode() ) {
																		$product_priceFix = wc_get_price_including_tax( $product, array(
																			'qty'   => 1,
																			'price' => $rule_role_price['discount_value'],
																		) );
																	} else {
																		$product_priceFix = wc_get_price_excluding_tax( $product, array(
																			'qty'   => 1,
																			'price' => $rule_role_price['discount_value'],
																		) );
																	}

																	//Aelia currency switcher compatibility
																	$product_priceFix = apply_filters('wc_aelia_cs_convert', $product_priceFix, $base_currency, $active_currency);

																	$price = wc_price($product_priceFix);

																} elseif ('fixed_increase' == $rule_role_price['discount_type'] ) {


																	if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																		$newprice = 0;
																		$newprice = $newprice + $rule_role_price['discount_value'];
																																
																	} else {

																		$newprice = $pro_price + $rule_role_price['discount_value'];
																	}

																	//Aelia currency switcher compatibility
																	$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);
																		
																	$price = wc_price($newprice);

																} elseif ('fixed_decrease' == $rule_role_price['discount_type'] ) {

																	if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																		$newprice = 0;
																			
																																
																	} else {

																		$newprice = $pro_price - $rule_role_price['discount_value'];
																	}

																	//Aelia currency switcher compatibility
																	$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																		
																	$price = wc_price($newprice);

																} elseif ('percentage_decrease' == $rule_role_price['discount_type'] ) {


																	if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																		$newprice = 0;
																			
																																
																	} else {

																		$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																		$newprice = $pro_price - $percent_price;
																	}

																	//Aelia currency switcher compatibility
																	$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);

																	$price = wc_price($newprice);

																		   

																} elseif ('percentage_increase' == $rule_role_price['discount_type'] ) {


																	if (empty($pro_price) || ( !empty($pro_price) && 0 == $pro_price )) {

																		$newprice = 0;
																			
																																
																	} else {

																		$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																		$newprice = $pro_price + $percent_price;
																	}

																	//Aelia currency switcher compatibility
																	$newprice = apply_filters('wc_aelia_cs_convert', $newprice, $base_currency, $active_currency);



																	$price = wc_price($newprice);


																		
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
					
				}    
			}



			if ( !empty( floatval( $newprice ) ) ) {

				if ( 'incl' == $this->get_tax_price_display_mode() ) {
					$product_price = wc_get_price_including_tax( $product, array(
						'qty'   => 1,
						'price' => $newprice,
					) );
				} else {
					$product_price = wc_get_price_excluding_tax( $product, array(
						'qty'   => 1,
						'price' => $newprice,
					) );
				}

				$price = wc_price( $product_price );

				//Aelia currency switcher compatibility
				$price = apply_filters('wc_aelia_cs_convert', $price, $base_currency, $active_currency);
			}


			return $price;    
		}

		public function get_tax_price_display_mode() {
			if (!empty(wc()->cart)) {
				if ( wc()->cart->get_customer() && wc()->cart->get_customer()->get_is_vat_exempt() ) {
					return 'excl';
				}

				return get_option( 'woocommerce_tax_display_cart' );
			}
		}
	}

	new Front_Class_Addify_Customer_And_Role_Pricing();
}
