<?php
/**
 * Discount Cart.
 *
 * @package Discount Cart By Total Value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Discount Cart Admin Class.
 * */
class Dcv_Discount_Cart_Admin {

	/**
	 * Constructor.
	 * */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'af_dcv_discount_style_scripts' ) );

		add_action( 'admin_menu', array( $this, 'add_cbd_submenu_b2b' ), 20 );

		add_action( 'add_meta_boxes', array( $this, 'dcv_custom_metabox' ) );

		add_action( 'save_post_discount_cart', array( $this, 'save_custom_metabox' ) );
	}
	/**
	 * Discount cart scripts.
	 * */
	public function af_dcv_discount_style_scripts() {

		wp_enqueue_style( 'select2-css', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ), array(), '5.7.2' );

		wp_enqueue_style( 'dcv-admin', DCV_URL . '/assest/css/af-dcv-admin-styling.css', array(), '1.0.0' );

		wp_enqueue_style( 'dcv_font_awesome', '////cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', false, '1.0', false );

		wp_enqueue_script( 'select2-js', plugins_url( 'assets/js/select2/select2.min.js', WC_PLUGIN_FILE ), array( 'jquery' ), '4.0.3', true );

		wp_enqueue_script( 'af_dc_js', DCV_URL . '/assest/js/af-dcv-discount.js', array( 'jquery' ), '1.0.0', false );

		ob_start();

		global $wp_roles;

		$af_dc_discount_wp_roles = $wp_roles->get_names();

		$af_dc_discount_wp_roles['guest'] = 'Guest';

		$af_dc_discount_wp_roles['for_all'] = 'For all';
		?>
		<tr>
			<td>
				<div class="meta-box-rule">
					<select  name="af_dis_detail[af_dcv_user_roles][]" class="af_user_roles_select af_select_box">
						<?php
						foreach ( $af_dc_discount_wp_roles as $key => $value ) {
							?>
							<option value="<?php echo esc_html( $key ); ?>">
								<?php echo esc_html( $value ); ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</td>			
			<td>
				<div class="meta-box-rule">
					<select name="af_dis_detail[dicount_type][]">
						<option value="fixed"> 
							<?php echo esc_html__( 'Fixed Discount', 'addify_b2b' ); ?>
						</option>
						<option value="percentage">
							<?php echo esc_html__( 'Percentage', 'addify_b2b' ); ?>
						</option>
					</select>
				</div>
			</td>			
			<td>
				<div class="meta-box-rule" >
					<input id="dcv_minimum" type="Number" name="af_dis_detail[min][]" min="0" pattern="^[0-9]+">
				</div>
			</td>
			<td>
				<div class="meta-box-rule">
					<input id="max_value" type="Number" name="af_dis_detail[max][]" min="0" pattern="^[0-9]+">
				</div>
			</td>
			<td>
				<div class="meta-box-rule">
					<input id="dcv_number" type="Number" min="0" pattern="^[0-9]+" name="af_dis_detail[discount_value][]" value="" placeholder="">
				</div>
			</td>
			<td><i id="af_close_btn_colour" class="af-close-row fa fa-close button button-primary button-large"></i></td>
		</tr>
		<?php
		$html = ob_get_clean();

		$af_arg = array(

			'admin_url' => admin_url( 'admin-ajax.php' ),

			'nonce'     => wp_create_nonce( 'af_dc_nonce' ),
			'tr_html'   => $html,
		);
		wp_localize_script( 'af_dc_js', 'php_vars', $af_arg );
	}


	public function add_cbd_submenu_b2b() {

		if (defined('AFB2B_PLUGIN_DIR')) {
			return;
		}

		add_submenu_page(
			'addify-b2b',
			__('Cart Discounts', 'addify_b2b'), 
			__('Cart Discounts', 'addify_b2b'),
			'manage_options',
			'edit.php?post_type=discount_cart',
			'',
			10
		);
	}

	
	/**
	 * Metaboxes.
	 * */
	public function dcv_custom_metabox() {

		add_meta_box(
			'af-dcv-discount-product-rest',
			esc_html__( 'Product Restrictions', 'addify_b2b' ),
			array( $this, 'af_dc_discount_product_restrict' ),
			'discount_cart'
		);

		add_meta_box(
			'af-dcv-discount-discount-rest',
			esc_html__( 'Discount Settings', 'addify_b2b' ),
			array( $this, 'af_dcv_discount_rest' ),
			'discount_cart'
		);

		add_meta_box(
			'af-dcv-discount-message-rest',
			esc_html__( 'Message Restrictions', 'addify_b2b' ),
			array( $this, 'af_dcv_message_rest' ),
			'discount_cart'
		);

		add_meta_box(
			'af-dcv-discount-date-rest',
			esc_html__( 'Date Restrictions', 'addify_b2b' ),
			array( $this, 'af_dcv_date_rest' ),
			'discount_cart'
		);
	}
	/**
	 * Metabox callback.
	 * */
	public function af_dc_discount_product_restrict() {

		include_once DCV_PLUGIN_DIR . '/include/admin/meta-boxes/rules/af-dcv-discount-product-restrictions.php';
	}
	/**
	 * Metabox callback.
	 * */
	public function af_dcv_discount_rest() {

		include_once DCV_PLUGIN_DIR . '/include/admin/meta-boxes/rules/af-dcv-discount-restrictions.php';
	}
	/**
	 * Metabox callback.
	 * */
	public function af_dcv_message_rest() {

		include_once DCV_PLUGIN_DIR . '/include/admin/meta-boxes/rules/af-dcv-discount-message-restrictions.php';
	}
	/**
	 * Metabox callback.
	 * */
	public function af_dcv_date_rest() {

		include_once DCV_PLUGIN_DIR . '/include/admin/meta-boxes/rules/af-dcv-discount-date-restrictions.php';
	}
	/**
	 * Save metabox values.
	 *
	 * @param int $post_id have post id.
	 * */
	public function save_custom_metabox( $post_id ) {

		$exclude_statuses = array(
			'auto-draft',
			'trash',
		);

		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

		if ( current_user_can( 'edit_post', $post_id ) && ! in_array( get_post_status( $post_id ), $exclude_statuses ) && ! is_ajax() && 'untrash' !== $action ) {

			$nonce = isset( $_POST['af_dc_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['af_dc_nonce'] ) ) : '';

			if ( ! wp_verify_nonce( $nonce, 'wp_verify_nonce' ) ) {

				die( esc_html__( 'Failed Ajax security check!', 'addify_b2b' ) );
			}

			$af_dcv_userroles = isset( $_POST['dcv_roles_checkboxs'] ) ? sanitize_meta( '', wp_unslash( $_POST['dcv_roles_checkboxs'] ), '' ) : array();

			update_post_meta( $post_id, 'dcv_roles_checkboxs', $af_dcv_userroles );

			$af_dcv_products = isset( $_POST['af_dcv_discount_products'] ) ? sanitize_meta( '', wp_unslash( $_POST['af_dcv_discount_products'] ), '' ) : array();

			update_post_meta( $post_id, 'af_dcv_discount_products', $af_dcv_products );

			$af_dcv_categories = isset( $_POST['af_dcv_discount_category'] ) ? sanitize_meta( '', wp_unslash( $_POST['af_dcv_discount_category'] ), '' ) : array();

			update_post_meta( $post_id, 'af_dcv_discount_category', $af_dcv_categories );

			$af_dcv_tags = isset( $_POST['af_dcv_discount_product_tag'] ) ? sanitize_meta( '', wp_unslash( $_POST['af_dcv_discount_product_tag'] ), '' ) : array();

			update_post_meta( $post_id, 'af_dcv_discount_product_tag', $af_dcv_tags );
			// discount metabox values.

			$af_coupons_enable = isset( $_POST['af_dcv_coupons_enable'] ) ? sanitize_text_field( wp_unslash( $_POST['af_dcv_coupons_enable'] ) ) : '';

			update_post_meta( $post_id, 'af_dcv_coupons_enable', $af_coupons_enable );

			$af_disable_btb = isset( $_POST['af_disable_btb'] ) ? sanitize_text_field( wp_unslash( $_POST['af_disable_btb'] ) ) : '';

			update_post_meta( $post_id, 'af_disable_btb', $af_disable_btb );

			$af_discount_message_check = isset( $_POST['dcv_message_check'] ) ? sanitize_text_field( wp_unslash( $_POST['dcv_message_check'] ) ) : '';

			update_post_meta( $post_id, 'dcv_message_check', $af_discount_message_check );

			$af_dcv_discount_for_prod_cart = isset( $_POST['af_dcv_discount_for_prod_cart'] ) ? sanitize_text_field( wp_unslash( $_POST['af_dcv_discount_for_prod_cart'] ) ) : '';

			update_post_meta( $post_id, 'af_dcv_discount_for_prod_cart', $af_dcv_discount_for_prod_cart );

			$af_discount_message = isset( $_POST['af_dcv_discount_notifi_message'] ) ? sanitize_text_field( wp_unslash( $_POST['af_dcv_discount_notifi_message'] ) ) : '';

			update_post_meta( $post_id, 'af_dcv_discount_notifi_message', $af_discount_message );

			$af_discount_succes_message = isset( $_POST['af_dcv_discount_success_message'] ) ? sanitize_text_field( wp_unslash( $_POST['af_dcv_discount_success_message'] ) ) : '';

			update_post_meta( $post_id, 'af_dcv_discount_success_message', $af_discount_succes_message );

			$af_dcv_discount_type = isset( $_POST['dcv_discount_type'] ) ? sanitize_text_field( wp_unslash( $_POST['dcv_discount_type'] ) ) : '';

			update_post_meta( $post_id, 'dcv_discount_type', $af_dcv_discount_type );

			$af_discount_value = isset( $_POST['af_dis_detail'] ) ? sanitize_meta( '', wp_unslash( $_POST['af_dis_detail'] ), '' ) : array();

			update_post_meta( $post_id, 'af_dis_detail', $af_discount_value );

			$dcv_discount_by_total_price = isset( $_POST['dcv_discount_by_total_price'] ) ? sanitize_text_field( wp_unslash( $_POST['dcv_discount_by_total_price'] ) ) : '';

			update_post_meta( $post_id, 'dcv_discount_by_total_price', $dcv_discount_by_total_price );

			$af_dcv_discount_product_method = isset( $_POST['af_dcv_discount_product_method'] ) ? sanitize_text_field( wp_unslash( $_POST['af_dcv_discount_product_method'] ) ) : '';

			update_post_meta( $post_id, 'af_dcv_discount_product_method', $af_dcv_discount_product_method );

			$af_discount_start_date = isset( $_POST['af_dcv_discount_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['af_dcv_discount_start_date'] ) ) : '';

			update_post_meta( $post_id, 'af_dcv_discount_start_date', $af_discount_start_date );

			$af_discount_end_date = isset( $_POST['af_dcv_discount_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['af_dcv_discount_end_date'] ) ) : '';

			update_post_meta( $post_id, 'af_dcv_discount_end_date', $af_discount_end_date );
		}
	}
}

new Dcv_Discount_Cart_Admin();

