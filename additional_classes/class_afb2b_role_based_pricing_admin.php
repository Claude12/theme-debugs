<?php

if (! defined('ABSPATH') ) {
	exit; // restict for direct access
}

if (! class_exists('Admin_Class_Addify_Customer_And_Role_Pricing') ) {

	class Admin_Class_Addify_Customer_And_Role_Pricing extends Addify_B2B_Plugin {
	
		public $allfetchedrules; 

		public function __construct() {

			$this->allfetchedrules = $this->csp_load();

			add_action('admin_enqueue_scripts', array( $this, 'csp_admin_assets' ));
			// Product Level
			// Create the custom tab
			add_filter('woocommerce_product_data_tabs', array( $this, 'create_csp_tab' ));
			// Add the custom fields
			add_action('woocommerce_product_data_panels', array( $this, 'display_csp_fields' ));
			// Save the custom fields
			add_action('woocommerce_process_product_meta', array( $this, 'save_csp_fields' ));

			// For Variable Products
			add_action('woocommerce_product_after_variable_attributes', array( $this, 'csp_variable_fields' ), 10, 3);
			add_action('woocommerce_save_product_variation', array( $this, 'csp_save_custom_field_variations' ), 10, 2);

			// Rule Based
			add_action('add_meta_boxes', array( $this, 'csp_add_custom_meta_box' ));
			add_action('save_post_csp_rules', array( $this, 'csp_add_custom_meta_save' ));
			add_filter('manage_csp_rules_posts_columns', array( $this, 'csp_rules_custom_columns' ));
			add_action('manage_csp_rules_posts_custom_column', array( $this, 'csp_rules_custom_column' ), 10, 2);

			add_action('wp_ajax_cspsearchProducts', array( $this, 'cspsearchProducts' ));
			add_action('wp_ajax_cspsearchUsers', array( $this, 'cspsearchUsers' ));

			//reset pricing template settings
			add_action('wp_ajax_afb2b_role_reset_pricing_template_settings', array( $this, 'afb2b_role_reset_pricing_template_settings' ));


			//Add Role Base Pring meta to product export

			add_filter('woocommerce_product_export_column_names', array( $this, 'csp_add_export_column' ));
			add_filter('woocommerce_product_export_product_default_columns', array( $this, 'csp_add_export_column' ));

			add_filter('woocommerce_product_export_product_column__cus_base_price', array( $this, 'csp_add_export_data_cus_base' ), 10, 2);
			add_filter('woocommerce_product_export_product_column__role_base_price', array( $this, 'csp_add_export_data_role_base' ), 10, 2);

			// Admin Order.
			add_action( 'woocommerce_ajax_order_items_added', array( $this, 'adjust_admin_order_prices' ), 100, 2 );
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

		public function adjust_admin_order_prices( $added_items, $order ) {

			foreach ( $order->get_items() as $item ) {

				if ( $order->get_user_id() && 'line_item' == $item->get_type()  ) {

					$product  = $item->get_product();
					$quantity = $item->get_quantity();

					$new_line_subtotal  = $item->get_subtotal();
					$new_line_subt_tax  = $item->get_subtotal_tax();
					$new_line_total     = $item->get_total();
					$new_line_total_tax = $item->get_total_tax();
					$taxes              = $item->get_taxes();

					$role_based_price = $this->get_role_based_price( $product, $quantity, $order->get_user() );

					// Update Order item prices
					$item->set_subtotal( $role_based_price * $quantity );
					$item->set_subtotal_tax( $new_line_subt_tax );
					$item->set_total( $role_based_price * $quantity );
					$item->set_total_tax( $new_line_total_tax );
					$item->set_taxes( $taxes );
					$item->save();
				}
			}

			$order->calculate_totals();
			$order->save();
		}

		public function get_role_based_price( $product, $quantity, $user ) {

			if ( empty( $user ) ) {
				return $product->get_price();
			}

			// get customer specific price
			$cus_base_price = get_post_meta( $product->get_id(), '_cus_base_price', true);

			if ( !empty( $cus_base_price ) ) {

				foreach ( $cus_base_price as $cus_price ) {

					if ( isset( $cus_price['customer_name'] ) && $user->ID == $cus_price['customer_name'] ) {

						$min_quantity = isset( $cus_price['min_qty'] ) ? $cus_price['min_qty'] : 0;
						$max_quantity = isset( $cus_price['max_qty'] ) ? $cus_price['max_qty'] : PHP_INT_MAX;
						$type         = isset( $cus_price['discount_type'] ) ? $cus_price['discount_type'] : 'fixed';
						$price        = isset( $cus_price['discount_value'] ) ? $cus_price['discount_value'] : 0;

						if ( empty( $price ) ) {
							continue;
						}

						if ( $this->check_quantities( $quantity, $min_quantity, $max_quantity ) ) {
							return $this->calculate_price( $price , $type, $product );
						}
					}
				}
			}

			// get role base price
			$role_base_price = get_post_meta( $product->get_id(), '_role_base_price', true);

			if (! empty($role_base_price) ) {

				foreach ( $role_base_price as $role_price ) {

					if (isset($role_price['user_role']) && in_array( $role_price['user_role'], $user->roles ) ) {

						$min_quantity = isset( $role_price['min_qty'] ) ? $role_price['min_qty'] : 0;
						$max_quantity = isset( $role_price['max_qty'] ) ? $role_price['max_qty'] : PHP_INT_MAX;
						$type         = isset( $role_price['discount_type'] ) ? $role_price['discount_type'] : 'fixed';
						$price        = isset( $role_price['discount_value'] ) ? $role_price['discount_value'] : 0;

						if ( empty( $price ) ) {
							continue;
						}

						if ( $this->check_quantities( $quantity, $min_quantity, $max_quantity ) ) {
							return $this->calculate_price( $price , $type, $product );
						}
					}
				}
			}

			if ( !empty( $this->allfetchedrules ) ) {

				$product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();

				foreach ( $this->allfetchedrules as $rule ) {

					$applied_on_all_products = get_post_meta($rule->ID, 'csp_apply_on_all_products', true);
					$products                = get_post_meta($rule->ID, 'csp_applied_on_products', true);
					$categories              = get_post_meta($rule->ID, 'csp_applied_on_categories', true);
					$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

					$istrue = false;

					if ('yes' == $applied_on_all_products ) {
						$istrue = true;
					} elseif (! empty($products) && in_array($product_id, $products) ) {
						$istrue = true;
					}


					if ( !empty($categories) && has_term( $categories, 'product_cat', $product_id ) ) {
						$istrue = true;
					}


					if (!empty($rbp_slected_brands) && has_term( $rbp_slected_brands, 'product_brand', $product_id ) ) {
						$istrue = true;
					}

					if ($istrue ) {

						// get Rule customer specifc price
						$rule_cus_base_price = get_post_meta($rule->ID, 'rcus_base_price', true);

						if (! empty($rule_cus_base_price) ) {

							foreach ( $rule_cus_base_price as $rule_cus_price ) {

								if (isset($rule_cus_price['customer_name']) && $user->ID == $rule_cus_price['customer_name'] ) {

									$min_quantity = isset( $rule_cus_price['min_qty'] ) ? $rule_cus_price['min_qty'] : 0;
									$max_quantity = isset( $rule_cus_price['max_qty'] ) ? $rule_cus_price['max_qty'] : PHP_INT_MAX;
									$type         = isset( $rule_cus_price['discount_type'] ) ? $rule_cus_price['discount_type'] : 'fixed';
									$price        = isset( $rule_cus_price['discount_value'] ) ? $rule_cus_price['discount_value'] : 0;

									if ( empty( $price ) ) {
										continue;
									}

									if ( $this->check_quantities( $quantity, $min_quantity, $max_quantity ) ) {
										return $this->calculate_price( $price , $type, $product );
									}
								}
							}
						}

						$rule_role_base_price = get_post_meta($rule->ID, 'rrole_base_price', true);

						if (! empty($rule_role_base_price) ) {

							foreach ( $rule_role_base_price as $rule_role_price ) {

								if ( isset($rule_role_price['user_role']) && in_array( $rule_role_price['user_role'], $user->roles ) ) {

									$min_quantity = isset( $rule_role_price['min_qty'] ) ? $rule_role_price['min_qty'] : 0;
									$max_quantity = isset( $rule_role_price['max_qty'] ) ? $rule_role_price['max_qty'] : PHP_INT_MAX;
									$type         = isset( $rule_role_price['discount_type'] ) ? $rule_role_price['discount_type'] : 'fixed';
									$price        = isset( $rule_role_price['discount_value'] ) ? $rule_role_price['discount_value'] : 0;

									if ( empty( $price ) ) {
										continue;
									}

									if ( $this->check_quantities( $quantity, $min_quantity, $max_quantity ) ) {
										return $this->calculate_price( $price , $type, $product );
									}
								}
							}
						}
					}
				}
			}

			return $product->get_price();
		}

		public function check_quantities( $quantity, $min_quantity = 0, $max_quantity = 0 ) {

			$flag = false;

			if ( empty( $min_quantity ) && empty( $max_quantity ) ) {
				return true;
			}

			if ( empty( $min_quantity ) && $quantity <= floatval( $max_quantity ) ) {
				return true;

			}

			if ( empty( $max_quantity ) && $quantity >= $min_quantity ) {
				return true;
			}

			if ( $min_quantity <= $quantity && $quantity <= $max_quantity ) {
				return true;
			}

			return false;
		}

		public function calculate_price( $price, $type, $product ) {

			$price      = floatval(  $price );
			$base_price = (float) $product->get_price();

			switch ( $type ) {

				case 'fixed_price':
					return $price;
				case 'fixed_increase':
					return $base_price + $price;
				case 'fixed_decrease':
					return $base_price - $price;
				case 'percentage_increase':
					return $base_price + ( $price * $base_price / 100 );
				case 'percentage_decrease':
					return $base_price - ( $price * $base_price / 100 );
			}

			return $base_price;
		}

		public function csp_add_export_column( $columns ) {

			// column slug => column name
			$columns['_cus_base_price']  = 'Customer Based Pricing';
			$columns['_role_base_price'] = 'Role Based Pricing';

			return $columns;
		}
		

		public function csp_add_export_data_cus_base( $value, $product ) {
			
			$value = serialize($product->get_meta('_cus_base_price', true, 'edit'));
			return $value;
		}

		public function csp_add_export_data_role_base( $value, $product ) {
			$value = serialize($product->get_meta('_role_base_price', true, 'edit'));
			return $value;
		}

		public function csp_admin_assets() {

			$screen = get_current_screen();

			if ('toplevel_page_addify-b2b' == $screen->id || 'edit-csp_rules' == $screen->id || 'csp_rules' == $screen->id || 'edit-product' == $screen->id || 'product' == $screen->id ) {

				wp_enqueue_style('addify_csp_admin_css', plugins_url('../assets/css/addify_csp_admin_css.css', __FILE__), false, '1.1');
				wp_enqueue_script('addify_csp_admin_js', plugins_url('../assets/js/addify_csp_admin_js.js', __FILE__), false, '1.1');
				$csp_data = array(
					'admin_url' => admin_url('admin-ajax.php'),
					'nonce'     => wp_create_nonce('afrolebase-ajax-nonce'),

				);
				wp_localize_script('addify_csp_admin_js', 'csp_php_vars', $csp_data);
				// select2 css and js
				wp_enqueue_script('jquery');
				wp_enqueue_style('addify_ps-select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', false, '1.0');
				wp_enqueue_style('addify_ps-select2-bscss', 'https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2-bootstrap.css', false, '1.0');
				wp_enqueue_script('addify_ps-select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', false, '1.0');
			}
		}

		public function create_csp_tab( $tabs ) {
			$tabs['addify_csp_customer'] = array(
				'label'    => esc_html__('Role Based Pricing(By Customers)', 'addify_b2b'), // The name of your panel
				'target'   => 'addify_csp_panel_customer', // Will be used to create an anchor link so needs to be unique
				'class'    => array( 'addify_csp_tab', 'show_if_simple' ), // Class for your panel tab - helps hide/show depending on product type
				'priority' => 80, // Where your panel will appear. By default, 70 is last item
			);

			$tabs['addify_csp_role'] = array(
				'label'    => esc_html__('Role Based Pricing(By User Roles)', 'addify_b2b'), // The name of your panel
				'target'   => 'addify_csp_panel_role', // Will be used to create an anchor link so needs to be unique
				'class'    => array( 'addify_csp_tab', 'show_if_simple' ), // Class for your panel tab - helps hide/show depending on product type
				'priority' => 80, // Where your panel will appear. By default, 70 is last item
			);
			return $tabs;
		}

		public function display_csp_fields() {

			global $post;

			$cus_base_prices  = get_post_meta($post->ID, '_cus_base_price', true);
			$role_base_prices = get_post_meta($post->ID, '_role_base_price', true);
			wp_nonce_field('csp_nonce_action', 'csp_nonce_field');

			include AFB2B_PLUGIN_DIR . 'includes/csp_product_level.php';
		}

		public function csp_variable_fields( $loop, $variation_data, $variation ) {

			$cus_base_prices  = get_post_meta($variation->ID, '_cus_base_price', true);
			$role_base_prices = get_post_meta($variation->ID, '_role_base_price', true);
			wp_nonce_field('csp_nonce_action', 'csp_nonce_field');

			include AFB2B_PLUGIN_DIR . 'includes/csp_product_level_variable_product.php';
		}

		public function save_csp_fields( $post_id ) {

			$product = wc_get_product($post_id);

			if ( 'variable' == $product->get_type() ) {
				return;
			}

			$retrieved_nonce = isset( $_REQUEST['csp_nonce_field'] ) ? sanitize_text_field($_REQUEST['csp_nonce_field']) : '';

			if (! wp_verify_nonce($retrieved_nonce, 'csp_nonce_action') ) {
				die( esc_html__('Failed security check', 'addify_b2b') );
			}

			if (isset($_POST['cus_base_price']) ) {

				$cus_base_price = sanitize_meta('', $_POST['cus_base_price'], '');
			} else {
				$cus_base_price = '';
			}

			if (! empty($cus_base_price) ) {

				$product->update_meta_data('_cus_base_price', $cus_base_price);
			} else {

				$product->delete_meta_data('_cus_base_price');
			}

			// role based

			if (isset($_POST['role_base_price']) ) {

				$role_base_price = sanitize_meta('', $_POST['role_base_price'], '');
			} else {
				$role_base_price = '';
			}

			if (! empty($role_base_price) ) {

				$product->update_meta_data('_role_base_price', $role_base_price);
			} else {

				$product->delete_meta_data('_role_base_price');
			}
			

			$product->save();
		}

		public function csp_save_custom_field_variations( $variation_id, $i ) {

			if (isset($_REQUEST['csp_nonce_field']) && ! empty($_REQUEST['csp_nonce_field']) ) {
				$retrieved_nonce = sanitize_text_field($_REQUEST['csp_nonce_field']);
			} else {
				$retrieved_nonce = 0;
			}

			if (! wp_verify_nonce($retrieved_nonce, 'csp_nonce_action') ) {
				die('Failed security check');
			}

			if (isset($_POST['cus_base_price'][ $variation_id ]) ) {
				$cus_base_price = sanitize_meta('', $_POST['cus_base_price'][ $variation_id ], '');
			} else {
				$cus_base_price = '';
			}

			if ('' != $cus_base_price ) {
				update_post_meta($variation_id, '_cus_base_price', $cus_base_price);
			} else {
				update_post_meta($variation_id, '_cus_base_price', '');
			}

			//role base
			if (isset($_POST['role_base_price'][ $variation_id ]) ) {
				$role_base_price = sanitize_meta('', $_POST['role_base_price'][ $variation_id ], '');
			} else {
				$role_base_price = '';
			}

			if ('' != $role_base_price ) {
				update_post_meta($variation_id, '_role_base_price', $role_base_price);
			} else {
				update_post_meta($variation_id, '_role_base_price', '');
			}
		}

		public function csp_add_custom_meta_box() {

			add_meta_box('csp-meta-box', esc_html__('Rule Details', 'addify_b2b'), array( $this, 'csp_meta_box_callback' ), 'csp_rules', 'normal', 'high', null);
		}

		public function csp_meta_box_callback() {

			global $post;
			wp_nonce_field('csp_nonce_action', 'csp_nonce_field');
			$rcus_base_price  = get_post_meta($post->ID, 'rcus_base_price', true);
			$rrole_base_price = get_post_meta($post->ID, 'rrole_base_price', true);

			$csp_applied_on_categories = get_post_meta($post->ID, 'csp_applied_on_categories', true);

			include AFB2B_PLUGIN_DIR . 'includes/csp_rule_level.php';
		}

		public function csp_add_custom_meta_save( $post_id ) {

			$exclude_statuses = array(
				'auto-draft',
				'trash',
			);

			$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

			if ( in_array( get_post_status($post_id), $exclude_statuses ) || is_ajax() || 'untrash' === $action ) {
				return;
			}

			if (! empty($_REQUEST['csp_nonce_field']) ) {
				$retrieved_nonce = sanitize_text_field($_REQUEST['csp_nonce_field']);
			} else {
				$retrieved_nonce = 0;
			}

			if (! wp_verify_nonce($retrieved_nonce, 'csp_nonce_action') ) {

				die('Failed security check');
			}

			if (! empty($_SESSION['allfetchedrules']) ) {

				$sess = sanitize_meta('', $_SESSION['allfetchedrules'], '');

				session_unset($sess);
			}

			remove_action('save_post_csp_rules', array( $this, 'csp_add_custom_meta_save' ));

			if (isset($_POST['csp_rule_priority']) ) {
				wp_update_post(
					array(
						'ID'         => intval($post_id),
						'menu_order' => sanitize_text_field($_POST['csp_rule_priority']),
					)
				);
			}

			add_action('save_post_csp_rules', array( $this, 'csp_add_custom_meta_save' ));

			if (isset($_POST['csp_apply_on_all_products']) ) {
				update_post_meta($post_id, 'csp_apply_on_all_products', sanitize_text_field($_POST['csp_apply_on_all_products']));
			} else {
				delete_post_meta($post_id, 'csp_apply_on_all_products', '');
			}

			if (isset($_POST['csp_applied_on_products']) ) {
				update_post_meta($post_id, 'csp_applied_on_products', sanitize_meta('', $_POST['csp_applied_on_products'], ''));
			} else {
				delete_post_meta($post_id, 'csp_applied_on_products');
			}

			if (isset($_POST['csp_applied_on_categories']) ) {
				update_post_meta($post_id, 'csp_applied_on_categories', sanitize_meta('', $_POST['csp_applied_on_categories'], ''));
			} else {
				delete_post_meta($post_id, 'csp_applied_on_categories');
			}

			if (isset($_POST['rcus_base_price']) ) {
				update_post_meta($post_id, 'rcus_base_price', sanitize_meta('', $_POST['rcus_base_price'], ''));
			} else {
				delete_post_meta($post_id, 'rcus_base_price');
			}

			if (isset($_POST['rrole_base_price']) ) {
				update_post_meta($post_id, 'rrole_base_price', sanitize_meta('', $_POST['rrole_base_price'], ''));
			} else {
				delete_post_meta($post_id, 'rrole_base_price');
			}

			if ( isset( $_POST['rbp_multi_brands'] ) ) {

				$rbp_brands = sanitize_meta( '', wp_unslash( $_POST['rbp_multi_brands'] ), '' );
			}

			if ( isset( $rbp_brands ) ) {
				update_post_meta( $post_id, 'rbp_multi_brands', wp_json_encode( $rbp_brands ) );
			} else {
				update_post_meta( $post_id, 'rbp_multi_brands', wp_json_encode( array() ) );
			}
		}

		public function csp_rules_custom_columns( $columns ) {

			unset($columns['date']);
			$columns['csp_rule_priority'] = esc_html__('Rule Priority', 'addify_b2b');
			$columns['date']              = esc_html__('Date Published', 'addify_b2b');

			return $columns;
		}

		public function csp_rules_custom_column( $column, $post_id ) {

			$postt = get_post($post_id);

			switch ( $column ) {
				case 'csp_rule_priority':
					echo esc_attr($postt->menu_order);
					break;
			}
		}


		public function cspsearchProducts() {

			$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

			if (! wp_verify_nonce($nonce, 'afrolebase-ajax-nonce') ) {
				die('Failed ajax security check!');
			}

			$pro = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';

			$data_array = array();
			$args       = array(
				'post_type'   => array( 'product' ),
				'post_status' => 'publish',
				'numberposts' => 50,
				's'           => $pro,
			);
			$pros       = get_posts($args);

			if (! empty($pros) ) {

				foreach ( $pros as $proo ) {

					$title        = ( mb_strlen($proo->post_title) > 50 ) ? mb_substr($proo->post_title, 0, 49) . '...' : $proo->post_title;
					$data_array[] = array( $proo->ID, $title ); // array( Post ID, Post Title )
				}
			}

			echo json_encode($data_array);

			die();
		}

		public function cspsearchUsers() {

			$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

			if (! wp_verify_nonce($nonce, 'afrolebase-ajax-nonce') ) {
				die('Failed ajax security check!');
			}

			$search = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';

			$data_array = array();
			$users      = new WP_User_Query(
				array(
					'search'         => '*' . esc_attr($search) . '*',
					'search_columns' => array(
						'user_login',
						'user_nicename',
						'user_email',
						'user_url',
					),
				)
			);

			$users_found = $users->get_results();

			if (! empty($users_found) ) {

				foreach ( $users_found as $user ) {

					$title        = $user->display_name . '(' . $user->user_email . ')';
					$data_array[] = array( $user->ID, $title ); // array( User ID, User name and email )
				}
			}

			echo json_encode($data_array);

			die();
		}

		public function afb2b_role_reset_pricing_template_settings() {

			$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
			
			if (! wp_verify_nonce($nonce, 'afrolebase-ajax-nonce') ) {
				die('Failed ajax security check!');
			}

			//general settings
			update_option('afb2b_role_pricing_design_type', 'default_template');
			update_option('afb2b_role_enable_template_heading', 'yes');
			update_option('afb2b_role_template_heading_text', 'Select your Deal');
			update_option('afb2b_role_template_heading_text_font_size', '28');
			update_option('afb2b_role_enable_template_icon', 'yes');
			update_option('afb2b_role_template_font_family', '');

			//default template settings
			update_option('afb2b_role_default_template_text_color', '#6d6d6d');
			update_option('afb2b_role_default_template_font_size', '18');

			//table settings
			update_option('afb2b_role_table_header_color', '#FFFFFF');
			update_option('afb2b_role_table_header_text_color', '#000000');
			update_option('afb2b_role_table_odd_rows_color', '#FFFFFF');
			update_option('afb2b_role_table_odd_rows_text_color', '#000000');
			update_option('afb2b_role_table_even_rows_color', '#FFFFFF');
			update_option('afb2b_role_table_even_rows_text_color', '#000000');
			update_option('afb2b_role_enable_table_border', 'yes');
			update_option('afb2b_role_table_border_color', '#CFCFCF');
			update_option('afb2b_role_table_header_font_size', '18');
			update_option('afb2b_role_table_rows_font_size', '16');

			// List settings
			update_option('afb2b_role_list_border_color', '#95B0EE');
			update_option('afb2b_role_list_background_color', '#FFFFFF');
			update_option('afb2b_role_list_text_color', '#000000');
			update_option('afb2b_role_selected_list_background_color', '#DFEBFF');
			update_option('afb2b_role_selected_list_text_color', '#000000');

			//card settings
			update_option('afb2b_role_card_border_color', '#A3B39E');
			update_option('afb2b_role_card_background_color', '#FFFFFF');
			update_option('afb2b_role_card_text_color', '#000000');
			update_option('afb2b_role_selected_card_border_color', '#27CA34');
			update_option('afb2b_role_enable_card_sale_tag', 'yes');
			update_option('afb2b_role_sale_tag_background_color', '#FF0000');
			update_option('afb2b_role_sale_tag_text_color', '#FFFFFF');

			die();
		}
	}

	new Admin_Class_Addify_Customer_And_Role_Pricing();

}
