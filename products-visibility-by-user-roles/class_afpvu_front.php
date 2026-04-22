<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( !class_exists( 'Addify_Products_Visibility_Front' ) ) {

	class Addify_Products_Visibility_Front extends Addify_Products_Visibility {

		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'afpvu_front_script' ));

			$g_boots = get_option('afpvu_allow_seo');

			if ( ! ( !empty($_SERVER['HTTP_USER_AGENT']) && 'googlebot' == $_SERVER['HTTP_USER_AGENT'] && 'yes' == $g_boots ) ) {

				add_action( 'woocommerce_product_query', array( $this, 'afpvu_custom_pre_get_posts_query' ), 200, 2 );
				add_filter( 'woocommerce_product_is_visible' , array( $this, 'afpvu_check_visibility_rules' ), 10, 2 );
				
				add_action( 'wp', array( $this, 'afpvu_redirect_to_custom_page' ));
				add_filter( 'page_template', array( $this, 'afpvu_custom_page_template' ));  
			}

			add_shortcode('addify-product-visibility-page', array( $this, 'af_product_vsibility_short_code' ) );
		}

		public function af_product_vsibility_short_code() {

			if ( is_user_logged_in() ) {
				$user      = wp_get_current_user();
				$roles     = ( array ) $user->roles;
				$curr_role = current( $roles );
			} else {

				$curr_role = 'guest';
			}

			if (!empty(get_option('afpvu_user_role_visibility'))) {

				$role_selected_data = get_option('afpvu_user_role_visibility');

			} else {

				$role_selected_data = array();

			}

			if (!empty($role_selected_data[ esc_attr( $curr_role ) ]['afpvu_enable_role'])) {

				$role_data = 'yes';

			} else {

				$role_data = 'no';
			}

			ob_start();

			?>
			<div class="pro_visib_msg">
			<?php
			if ('yes' == $role_data) {

				if (!empty($role_selected_data[ esc_attr( $curr_role ) ]['afpvu_custom_message_role'])) {

					echo wp_kses_post( $role_selected_data[ esc_attr( $curr_role ) ]['afpvu_custom_message_role'] ); 
				}

			} elseif ('no' == $role_data) {

				echo wp_kses_post( get_option( 'afpvu_global_custom_msg' ) ); 
			}
				
			?>
			</div>
			<?php
			return wp_kses_post( wpautop( wptexturize( ob_get_clean() ) ) );
		}

		public function afpvu_front_script() {

			wp_enqueue_style( 'afpvu-front', plugins_url( '/assets/css/afpvu_front.css', __FILE__ ), false, '1.0' );
		}

		public function afpvu_product_hidden( $product_id, $applied_products, $applied_categories, $show_hide ) {

			if ( 'hide' === $show_hide ) {

				if ( in_array( $product_id, $applied_products ) ) {
					return true;
				}

				foreach ($applied_categories as $cat) {
					if ( !empty( $cat ) && has_term( $cat, 'product_cat', $product_id ) ) {
						return true;
					}
				}

				return false;

			} else {

				if ( in_array( $product_id, $applied_products ) ) {
					return false;
				}

				foreach ($applied_categories as $cat) {
					if ( !empty( $cat ) && has_term( $cat, 'product_cat', $product_id ) ) {
						return false;
					}
				}

				return true;
			}
		}

		public function afpvu_check_visibility_rules( $visible, $product_id ) {

			if ( did_action('addify_query_visibility_applied') ) {
				return $visible;
			}

			$product             = wc_get_product( $product_id );
			$afpvu_enable_global = get_option('afpvu_enable_global');
			$curr_role           = is_user_logged_in() ? current( wp_get_current_user()->roles ) : 'guest';
			$role_selected_data  = (array) get_option('afpvu_user_role_visibility');

			if ( empty( $role_selected_data ) && 'yes' !== $afpvu_enable_global ) {
				return $visible;
			}

			$role_data = isset( $role_selected_data[ $curr_role ]['afpvu_enable_role'] ) ? $role_selected_data[ $curr_role ]['afpvu_enable_role'] : 'no';

			if ( 'yes' === $role_data ) {

				$_data                    = $role_selected_data[ $curr_role ];
				$afpvu_show_hide          = isset( $_data['afpvu_show_hide_role'] ) ? $_data['afpvu_show_hide_role'] : 'hide' ;
				$afpvu_applied_products   = isset( $_data['afpvu_applied_products_role'] ) ? (array) $_data['afpvu_applied_products_role'] : array() ;
				$afpvu_applied_categories = isset( $_data['afpvu_applied_categories_role'] ) ? (array) $_data['afpvu_applied_categories_role'] : array();

				if ( $this->afpvu_product_hidden( $product_id, $afpvu_applied_products, $afpvu_applied_categories, $afpvu_show_hide ) ) {
					return false;
				}

				return $visible;
			}

			if ( 'yes' === $afpvu_enable_global ) {

				$afpvu_show_hide          = get_option('afpvu_show_hide');
				$afpvu_applied_products   = (array) get_option('afpvu_applied_products');
				$afpvu_applied_categories = (array) get_option('afpvu_applied_categories');

				if ( $this->afpvu_product_hidden( $product_id, $afpvu_applied_products, $afpvu_applied_categories, $afpvu_show_hide ) ) {
					return false;
				}

				return $visible;
			}

			return $visible;
		}

		public function afpvu_custom_pre_get_posts_query( $q ) {

			global $product;

			$afpvu_enable_global = get_option('afpvu_enable_global');
			$curr_role           = is_user_logged_in() ? current( wp_get_current_user()->roles ) : 'guest';
			$role_selected_data  = (array) get_option('afpvu_user_role_visibility');

			if ( empty( $role_selected_data ) && 'yes' !== $afpvu_enable_global ) {
				return;
			}

			$role_data = isset( $role_selected_data[ $curr_role ]['afpvu_enable_role'] ) ? $role_selected_data[ $curr_role ]['afpvu_enable_role'] : 'no';

			if ( 'yes' === $afpvu_enable_global ) {

				$afpvu_show_hide          = get_option('afpvu_show_hide');
				$afpvu_applied_products   = (array) get_option('afpvu_applied_products');
				$afpvu_applied_categories = (array) get_option('afpvu_applied_categories');
			}

			if ( 'yes' === $role_data ) {

				$_data                    = $role_selected_data[ $curr_role ];
				$afpvu_show_hide          = isset( $_data['afpvu_show_hide_role'] ) ? $_data['afpvu_show_hide_role'] : 'hide' ;
				$afpvu_applied_products   = isset( $_data['afpvu_applied_products_role'] ) ? (array) $_data['afpvu_applied_products_role'] : array() ;
				$afpvu_applied_categories = isset( $_data['afpvu_applied_categories_role'] ) ? (array) $_data['afpvu_applied_categories_role'] : array();
			}

			if ( empty( $afpvu_applied_products ) && empty( $afpvu_applied_categories ) ) {
				return;
			}

			$products_ids = array();

			if ( !empty($afpvu_applied_categories) ) {
								
				$product_args = array(
					'numberposts' => -1,
					'post_status' => array( 'publish' ),
					'post_type'   => array( 'product' ), //skip types
					'fields'      => 'ids',
				);

				$product_args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'id',
						'terms'    => $afpvu_applied_categories,
						'operator' => 'IN',
					),
				);

				$products_ids = (array) get_posts($product_args);
			}

			$afpvu_applied_products = array_merge( (array) $afpvu_applied_products, (array) $products_ids );

			if (!empty($afpvu_show_hide) && 'hide' == $afpvu_show_hide) {

				$post__not_in = (array) $q->get('post__not_in');
				$q->set('post__not_in', array_merge($post__not_in, $afpvu_applied_products));

			} elseif (!empty($afpvu_show_hide) && 'show' == $afpvu_show_hide) {

				$q->set('post__in', $afpvu_applied_products);
			}
			do_action('addify_query_visibility_applied', $q );
		}


		public function afpvu_redirect_to_custom_page() {

			global $wp_query, $product;

			if (!empty(get_option('afpvu_enable_global'))) {

				$afpvu_enable_global = get_option('afpvu_enable_global');

			} else {

				$afpvu_enable_global = 'no';

			}

			if (is_shop()) {
				$Page_ID = wc_get_page_id('shop');
			} else {
				$Page_ID = $wp_query->get_queried_object_id();
			}

			if ( is_user_logged_in() ) {
				$user      = wp_get_current_user();
				$roles     = ( array ) $user->roles;
				$curr_role = current( $roles );
			} else {

				$curr_role = 'guest';
			}

			if (!empty(get_option('afpvu_user_role_visibility'))) {

				$role_selected_data = get_option('afpvu_user_role_visibility');

			} else {

				$role_selected_data = array();

			}

			if (!empty($role_selected_data[ esc_attr( $curr_role ) ]['afpvu_enable_role'])) {

				$role_data = 'yes';

			} else {

				$role_data = 'no';
			}

			//Global Visibility
			if ('no' == $role_data && 'yes' == $afpvu_enable_global) { //Check if current user role have role based visibility enabled
				$afpvu_show_hide = get_option('afpvu_show_hide');

				
				if (!empty(get_option('afpvu_applied_products'))) {
					$afpvu_applied_products = get_option('afpvu_applied_products');
				} else {
					$afpvu_applied_products = array();
				}
				if (!empty(get_option('afpvu_applied_categories'))) {
					$afpvu_applied_categories = get_option('afpvu_applied_categories');
				} else {
					$afpvu_applied_categories = array();
				}

				if ('custom_url' == get_option('afpvu_global_redirection_mode')) {

					$redirect_url =  get_option('afpvu_global_custom_url');

				} else {

					$redirect_url =  get_permalink( get_page_by_path( 'af-product-visibility' ) );

				}

				

			} elseif ('yes' == $role_data) {

				$afpvu_show_hide = $role_selected_data[ esc_attr( $curr_role ) ]['afpvu_show_hide_role'];

				if (!empty($role_selected_data[ esc_attr( $curr_role ) ]['afpvu_applied_products_role'])) {
					$afpvu_applied_products = $role_selected_data[ esc_attr( $curr_role ) ]['afpvu_applied_products_role'];
				} else {
					$afpvu_applied_products = array();
				}
				if (!empty($role_selected_data[ esc_attr( $curr_role ) ]['afpvu_applied_categories_role'])) {
					$afpvu_applied_categories = $role_selected_data[ esc_attr( $curr_role ) ]['afpvu_applied_categories_role'];
				} else {
					$afpvu_applied_categories = array();
				}

				if ('custom_url' == $role_selected_data[ esc_attr( $curr_role ) ]['afpvu_role_redirection_mode']) {

					$redirect_url =  $role_selected_data[ esc_attr( $curr_role ) ]['afpvu_role_custom_url'];

				} else {

					$redirect_url =  get_permalink( get_page_by_path( 'af-product-visibility' ) );

				}
			}

			

			//Products

			if ( !empty($afpvu_show_hide) && 'hide' == $afpvu_show_hide) {

				if ( is_array($afpvu_applied_products) && in_array($Page_ID, $afpvu_applied_products)) {
					
					
					wp_safe_redirect($redirect_url); 
					exit();

				}

			} elseif ( !empty($afpvu_show_hide) && 'show' == $afpvu_show_hide) {
				
				$af_visibility_page_id = get_page_by_path('af-product-visibility');
				
				if ( is_array($afpvu_applied_products) && ( in_array($Page_ID, $afpvu_applied_products)  || $Page_ID == $af_visibility_page_id->ID )) { 

					echo '';
					return;

				} elseif ( empty($afpvu_applied_categories) ) {
					
					if ( is_product() ) {
						
						wp_safe_redirect($redirect_url); 
						exit();
					}

				}


			}
				
			//Categories
			if ( !empty($afpvu_show_hide) && 'hide' == $afpvu_show_hide) {
				
				if (!is_product() && is_product_category()) {
					if ( is_array($afpvu_applied_categories) && in_array($Page_ID, $afpvu_applied_categories)) {
				
						
						wp_safe_redirect($redirect_url); 
						exit();

					}
				}

				//Category Products
				
				if (is_array($afpvu_applied_categories)) {
					
					foreach ($afpvu_applied_categories as $cat) {
						
						if ( has_term( $cat , 'product_cat', $Page_ID ) ) {

							
							wp_safe_redirect($redirect_url); 
							exit();

						}

					}
				}


			} elseif ( !empty($afpvu_show_hide) && 'show' == $afpvu_show_hide) {
				
				if (!is_product() && is_product_category()) {
					$af_visibility_page_id = get_page_by_path('af-product-visibility');
					if ( is_array($afpvu_applied_categories) && ( in_array($Page_ID, $afpvu_applied_categories)  || $Page_ID == $af_visibility_page_id->ID )) {

						echo '';

					} elseif ( !is_shop() && is_archive()) {

							
							wp_safe_redirect($redirect_url); 
							exit();

					}
				}

				//Category Products

				if (is_array($afpvu_applied_categories)) {
					$cat_match = false;
					foreach ($afpvu_applied_categories as $cat) {

						if ( ( has_term( $cat , 'product_cat', $Page_ID ) ) || $Page_ID == $af_visibility_page_id->ID) {

							echo '';
							return;

						}

					}
					
					if ( is_product()) {
						
						wp_safe_redirect($redirect_url); 
						exit();
					}
					
				}
				

			}



			//End Global Visibility
		}


		public function afpvu_custom_page_template( $page_template ) {

			global $wp;
			$afpvu_visibility_page =  get_page_by_path( 'af-product-visibility' );
			if (!empty($afpvu_visibility_page) && ( home_url(add_query_arg(array(), $wp->request)) === $afpvu_visibility_page->guid )) {
				$page_template = AFB2B_PLUGIN_DIR . 'templates/product_visibility_page.php';
			}
			return $page_template;
		}
	}

	new Addify_Products_Visibility_Front();
}
