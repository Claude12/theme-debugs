<?php
/**
 * Quote Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/addify-quote-request-page.php.
 *
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

$quotes = array();

if (!empty(WC()->session)) {

	$af_quote = new AF_R_F_Q_Quote( WC()->session->get( 'quotes' ) );
}
if (!empty(WC()->session)) {
	$quotes = WC()->session->get( 'quotes' );
}

if ( ! empty( $quotes ) ) {

	foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

		if ( isset( $quote_item['quantity'] ) && empty( $quote_item['quantity'] ) ) {

			unset( $quotes[ $quote_item_key ] );
		}

		if ( ! isset( $quote_item['data'] ) ) {
			unset( $quotes[ $quote_item_key ] );
		}
	}

	WC()->session->set( 'quotes', $quotes );
}
?>
<div class="woocommerce adf-request-quote-page <?php echo !empty(get_option('afrfq_select_page_template')) ? esc_attr(get_option('afrfq_select_page_template') ) : 'template_one'; ?>">
	<div class="woocommerce-notices-wrapper"><?php wc_print_notices(); ?></div>
	<style type="text/css">
		.adf-request-quote-page.template_one{
			width: 100%!important;
			max-width: 1000px!important;
		}
		.adf-request-quote-page.template_two{
			width: 100%!important;
			max-width:1160px!important
		}
	</style>
<?php

if ( !empty( $quotes ) ) {

	$total          = 0;
	$user           = null;
	$user_name      = '';
	$user_email_add = '';

	if ( is_user_logged_in() ) {
		$user = wp_get_current_user(); // object.
		if ( '' == $user->user_firstname && '' == $user->user_lastname ) {
			$user_name = $user->nickname; // probably admin user.
		} elseif ( '' == $user->user_firstname || '' == $user->user_lastname ) {
			$user_name = trim( $user->user_firstname . ' ' . $user->user_lastname );
		} else {
			$user_name = trim( $user->user_firstname . ' ' . $user->user_lastname );
		}

		$user_email_add = $user->user_email;
	}

	do_action( 'addify_before_quote' );


	$allowed_user_roles = get_option('afrfq_enable_for_specific_user_role');

	if (is_user_logged_in()) {
		$user = wp_get_current_user(); // object.
		$user_role = $user->roles;
	} else {
		$user_role = array( 'guest' );
	}

	$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
	$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
	$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;

	$text_color                      = get_option( 'afrfq_table_header_color' );
	$bg_color                        = get_option( 'afrfq_table_heade_bg_color' );
	$afrfq_enable_table_header_color = get_option( 'afrfq_enable_table_header_color' );
	?>
	<div class="adf-main-qoute-page">
	
		<style type="text/css">
			<?php
				$current_theme = wp_get_theme();
				$parent_theme  = $current_theme->parent();
			?>
			.addify-quote-form__contents tr, .addify-quote-form .cart-collaterals .cart_totals tr{
				height: auto!important;
				border: 0!important;
			}
			.afrq-menu-item{
				height: 100%;
			}
			
			.offered-price .offered-price-input, .product-quantity input.qty{
				background: no-repeat;
				box-shadow: none;
				border: 1px solid #99939333;
				border-radius: 1px;
				font-size: 13px!important;
				height: auto!important;
				line-height: 1.618;
				text-align: center;
				width: 50px !important;
				padding: 3px 0 3px 4px !important;
			}
			.addify-quote-form__contents .product-remove{
				text-align: center;
				width: 48px!important;
				border-top-left-radius: 7px;
				padding: 10px!important;
			}
			.addify-quote-form__contents th:last-child{
				border-top-right-radius: 7px;
			}
			.addify-quote-form__contents th{
				<?php if ( 'yes' == $afrfq_enable_table_header_color) { ?>
					background: <?php echo esc_html( $bg_color ); ?>!important;
					color: <?php echo esc_html( $text_color ); ?>!important;
				<?php } ?>
				font-size: 14px;
				line-height: 24px;
				vertical-align: middle;
				text-align: center!important;
					text-transform: capitalize;
				padding: 13px 13px !important;
				color: #000;
				vertical-align: middle;
			}
			.addify-quote-form__contents td{
				padding: 15px 13px !important;
				font-size: 14px;
				line-height: 24px;
				color: #000;
				border:0!important;
				vertical-align: middle;
				text-align: center!important;
			}
			.addify-quote-form__contents .product-name small b{
				margin-right: 4px;
			}
			.addify-quote-form.template_one .addify-quote-form__contents{
				border: 1px solid #d3d3d34d;
				border-radius: 7px;
			}
			.addify-quote-form__contents .product-name{
				text-align: left!important;
			}
			.addify-quote-form .cart_totals h2, .af_quote_fields h2{
				font-size: 21px;
				border: 0!important;
				line-height: 26px;
				margin: 0 0 15px 0;
				font-weight: 600;
				padding: 0;
			}
			.addify-quote-form.template_one .cart_totals table tr:first-child td,
			.addify-quote-form.template_one .cart_totals table tr:first-child th{
				padding-top: 15px!important;
			}
			.addify-quote-form.template_one .cart_totals table tr:last-child td,
			.addify-quote-form.template_one .cart_totals table tr:last-child th{
				padding-bottom: 15px!important;
			}
			.addify-quote-form.template_one .cart_totals table{
				border: 1px solid #d3d3d35c;
			}
			.addify-quote-form.template_one .product-name p{
				margin: 0!important;
			}
			.addify-quote-form .table_quote_totals th{
				text-align: left;
				background: none;
				font-size: 15px;
					padding: 8px 15px 7px!important;
				line-height: 25px;
				color: #000;
				border: 0!important;
			}
			.addify-quote-form .table_quote_totals th:last-child{
				padding: 8px 15px 10px!important
			}

			.addify-quote-form .table_quote_totals tr:last-child th{
				border-bottom-left-radius: 7px;
			}
			.addify-quote-form .table_quote_totals tr:last-child td{
				border-bottom-right-radius: 7px;
			}
			.addify-quote-form .table_quote_totals td{
				font-size: 15px;
				padding: 8px 15px 7px;
				line-height: 25px;
				border: 0!important;
				color: #000;
			}
			.div.af_quote_fields{
				border: 1px solid #d3d3d34f;
			}
			div.af_quote_fields input[type="text"], div.af_quote_fields input[type="email"], div.af_quote_fields input[type="time"], div.af_quote_fields input[type="date"], div.af_quote_fields input[type="datetime-local"], div.af_quote_fields select, div.af_quote_fields textarea,
			div.af_quote_fields input[type="number"]{
				box-shadow: none;
				background: #fff;
				border: 1px solid #d3d3d373;
				font-size: 14px;
				box-sizing: border-box;
				height: 42px;
				padding-left: 10px;
				line-height: 26px;
				width: 100%;
			}
			div.af_quote_fields select, div.af_quote_fields input[type="time"], div.af_quote_fields input[type="date"],
			div.af_quote_fields input[type="datetime-local"]{
				padding: 6px;
				height: 42px;
			}
			div.af_quote_fields td{
				padding: 0!important;
			}
			div.af_quote_fields label{
				display: block;
				margin-bottom: 7px;
				font-size: 15px;
				line-height: 25px;
				font-weight: 600;
			}
			table.quote-fields tr{
				border: none!important;
			}
			.addify-quote-form table.addify-quote-form__contents{
					margin-bottom: 25px;
			}
			.addify-quote-form .cart-collaterals .cart_totals{
				width: 100%;
				max-width: 47%;
				border: 0!important;
				padding: 0!important;
				margin: 0 0 0 auto!important;
			}
			.af-quote-field-table .form_row{
				text-align: right;
				display: block;
				clear: both;
				float: none;
			}
			.adf_full_width{
				width: 100%;
				flex: 0 0 100%;
				padding: 0!important;
				margin: 0 0 20px;
			}
			.adf_half_width{
				width: 48%;
				padding: 0!important;
				flex: 0 0 48%;
				margin: 0 0 20px;
			}
			.af-quote-field-table .button.addify_checkout_place_quote{
				float: none!important;
			}
			div.af_quote_fields{
				border-top: 1px solid #d3d3d373;
				padding-top: 35px;
				width: 100% !important;
				margin-top: 19px;
			}
			.addify-quote-form.template_one .af-quote-field-table{
				width: 100% !important;
				max-width: 560px !important;
			}
			.addify-quote-form.template_one .select2-container--default .select2-selection--multiple,
			.addify-quote-form.template_one .select2-container--default .select2-selection--single{
				height: 42px;
				border-radius: 0!important;
				border: 1px solid #d3d3d373;
			}
			.addify-quote-form.template_one .select2-container--default .select2-selection--single .select2-selection__rendered{
				font-size: 14px;
				line-height: 40px;
			}
			.addify-quote-form.template_one .select2-container--default .select2-selection--single .select2-selection__arrow{
				height: 42px;
			}
			.quote-fields{
				display: flex;
				flex-wrap: wrap;
				justify-content: space-between;
			}
			.adf-radio-btn, .adf-chekboxes{
				display: flex !important;
				gap: 7px;
				font-size: 14px !important;
				align-items: center;
				font-weight: normal ! Important;
			}

			/* Template 2*/
			.addify-quote-form.template_two{
				display: flex;
				justify-content: space-between;
			}
			.addify-quote-form.template_two .adf-quote-detail-wrap{
				width: 58%!important;
				overflow-x:scroll;
			}
			.addify-quote-form.template_two .af_quote_fields{
				width: 38%!important;
				padding: 25px;
				box-sizing: border-box!important;
				background: #f6f7f7;
				margin-top: 0!important;
				border: none;
			}
			.addify-quote-form .addify-option-field input[type="file"]{
				font-size: 14px;
			}
			.addify_checkout_place_quote{
				font-size: 15px;
				padding: 8px 19px;
				line-height: 24px;
				font-weight: 600;
			}
			.adf-term-conditon{
				display: flex;
				align-items: center;
				gap: 8px;
				font-size: 14px;
				line-height: 24px;
			}
			table.addify-quote-form__contents td.product-remove a.remove:before{
				content: none!important;
			}
			table.addify-quote-form__contents td.product-remove a.remove{
				font-size: 23px;
				text-decoration: none;
				color: #000;
				text-indent: inherit;
				text-align: center;
			}
			@media screen and (max-width:765px){
				
				.addify-quote-form.template_two{
					display: block!important;
				}
				form.addify-quote-form.template_two table.addify-quote-form__contents td:not(.product-thumbnail){
					text-align: right!important;
					padding: 10px 10px !important;
					border: 1px solid #ececec9c ! Important;
				}
				form.addify-quote-form.template_one table.addify-quote-form__contents td:not(.product-thumbnail){
					text-align: right!important;
				}
				.addify-quote-form__contents .product-thumbnail .adf-product-remove,
				.addify-quote-form__contents .product-thumbnail{
					text-align: center!important;
				}
				.addify-quote-form.template_two .cart_totals table tr td, .addify-quote-form__contents .product-thumbnail{
					padding: 12px!important;
					border: 1px solid #ececec9c ! Important;
				}
				.addify-quote-form.template_two .adf-quote-detail-wrap,
				.addify-quote-form.template_two .af_quote_fields,
				table.addify-quote-form__contents .product-thumbnail,
				.template_two .product-price.offered-price, .template_two .product-subtotal{
					width: 100%!important;
				}
				.addify-quote-form.template_two .cart_totals h2{
					margin-bottom: 22px!important;
				}
				.adf-multi-select{
					box-sizing: border-box;
					width: inherit!important;
				}
				.addify-quote-form .cart-collaterals .cart_totals{
					max-width: 100%;
				}
				.addify-quote-form .adf_half_width {
					width: 100%;
					flex: 0 0 100%;
				}
				table.addify-quote-form__contents td.product-remove a.remove{
					position: static!important;
					margin-left: auto;
					width: 100%!important;
				}
				<?php if ($current_theme->get('Name') === 'Avada' || ( $parent_theme && $parent_theme->get('Name') === 'Avada' )) { ?>
					form.addify-quote-form.template_one table.addify-quote-form__contents td:not(.product-thumbnail),
					form.addify-quote-form.template_two table.addify-quote-form__contents td:not(.product-thumbnail){
						text-align: center !important;
					}
					.addify-quote-form__contents.template-two .product-quantity .quantity{
						margin: 0 auto;
					}
					form.addify-quote-form.template_one table.addify-quote-form__contents td.product-quantity .quantity{
						width: 100px;
					}
				<?php
				}
				if ($current_theme->get('Name') === 'Woodmart' || ( $parent_theme && $parent_theme->get('Name') === 'Woodmart' )) {
					?>
					.addify-quote-form .adf-quote-detail-wrap{
						overflow-x: scroll;
					}
					form.addify-quote-form.template_one .product-thumbnail img{
							max-width: 78px;
					}
					form.addify-quote-form.template_one table.addify-quote-form__contents td:not(.product-thumbnail){
						text-align: left!important;
					}
					.addify-quote-form__contents .product-thumbnail{
						border:0!important;
					}
					form.addify-quote-form.template_two table.addify-quote-form__contents td:not(.product-thumbnail),
					.addify-quote-form.template_two .cart_totals table tr td{
						text-align: center!important;
						border: 0!important;
					}
					.addify-quote-form__contents.template-two th.product-thumbnail{
						border-bottom: 1px solid #a09f9f9c !Important;
					}
					form.addify-quote-form.template_two table.addify-quote-form__contents td.product-name{
						text-align: left !important;
					}
			<?php
				} 
				if ($current_theme->get('Name') === 'Divi' || ( $parent_theme && $parent_theme->get('Name') === 'Divi' )) {
					?>
					table.addify-quote-form__contents .product-thumbnail{
						display: block!important;
					}

					table.addify-quote-form__contents .product-thumbnail img{
						width: 50px!important;
					}
					table.addify-quote-form__contents .product-thumbnail a{
						display: block!important;
						text-align: center!important;
					}
					.woocommerce table.shop_table_responsive tr td.product-thumbnail::before {
						content: "" !important;
					}
				<?php } ?>
			}
			@media screen and (min-width:766px) and (max-width:1000px){
				.addify-quote-form.template_two{
					display: block!important;
				}
				.addify-quote-form.template_two .adf-quote-detail-wrap,
				.addify-quote-form.template_two .af_quote_fields{
					width: 100%!important;
				}
				.addify-quote-form .cart-collaterals .cart_totals{
					max-width: 100%;
				}
				<?php
				if ($current_theme->get('Name') === 'Astra' || ( $parent_theme && $parent_theme->get('Name') === 'Astra' )) {
					?>
					form.addify-quote-form.template_two table.addify-quote-form__contents td, .addify-quote-form.template_two .cart_totals table tr td{
						padding: 12px!important;
						border: 1px solid #ececec9c ! Important;
					}
					.addify-quote-form.template_two .cart_totals h2{
						margin-bottom: 22px!important;
					}
					table.addify-quote-form__contents .product-thumbnail, table.addify-quote-form__contents .product-remove{
						width: 100%!important;
					}
				<?php } ?>
				<?php if ($current_theme->get('Name') === 'Avada' || ( $parent_theme && $parent_theme->get('Name') === 'Avada' )) { ?>
					form.addify-quote-form.template_one table.addify-quote-form__contents td:not(.product-thumbnail),
					.addify-quote-form__contents .product-name{
						text-align: center !important;
					}
					.addify-quote-form__contents.template-two .product-quantity .quantity{
						margin: 0 auto;
					}
					.shop_table_responsive.woocommerce-cart-form__contents .product-thumbnail{
						width: -webkit-fill-available;
					}
					form.addify-quote-form.template_one table.addify-quote-form__contents td.product-quantity .quantity{
						width: 100px;
					}
				<?php
				}
				if ($current_theme->get('Name') === 'Woodmart' || ( $parent_theme && $parent_theme->get('Name') === 'Woodmart' )) {
					?>
				.addify-quote-form.template_one .addify-quote-form__contents td.product-quantity{
					width: 140px;
				}
				<?php } ?>
		}
		
		</style>
		<form class="woocommerce-cart-form addify-quote-form <?php echo !empty(get_option('afrfq_select_page_template')) ? esc_attr(get_option('afrfq_select_page_template') ) : 'template_one'; ?>" data-form-type="page" method="post" enctype="multipart/form-data">
			<div class="adf-quote-detail-wrap">
				<?php

				if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-table.php' ) ) {

					include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-table.php';

				} else {

					wc_get_template(
						'quote/quote-table.php',
						array(),
						'/woocommerce/addify/rfq/',
						AFRFQ_PLUGIN_DIR . 'templates/'
					);
				}

				?>

				<?php do_action( 'addify_before_quote_collaterals' ); ?>
				<?php if ( $price_display || $of_price_display ) : ?>
					<div class="cart-collaterals">
						<?php
							/**
							 * Quote collateral's hook.
							 */
							do_action( 'addify_quote_collaterals' );
						?>
						<div class="cart_totals">

							<?php do_action( 'addify_rfq_before_quote_totals' ); ?>

							<h2><?php esc_html_e( 'Quote totals', 'addify_b2b' ); ?></h2>

							<?php
							if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-totals-table.php' ) ) {

								include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-totals-table.php';

							} else {

								wc_get_template(
									'quote/quote-totals-table.php',
									array(),
									'/woocommerce/addify/rfq/',
									AFRFQ_PLUGIN_DIR . 'templates/'
								);
							}
							?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php do_action( 'addify_after_quote' ); ?>

			<div class="af_quote_fields">

				<div class="af-quote-field-table">

				<?php
				if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-fields.php' ) ) {

					include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-fields.php';

				} else {

					wc_get_template(
						'quote/quote-fields.php',
						array(),
						'/woocommerce/addify/rfq/',
						AFRFQ_PLUGIN_DIR . 'templates/'
					);
				}
				?>

				<?php do_action( 'addify_after_quote_fields' ); ?>

				<?php if ( 'yes' == get_option( 'afrfq_enable_captcha' ) ) { ?>

					<?php if ( ! empty( get_option( 'afrfq_site_key' ) ) ) { ?>

						<div class="form_row">
							<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( get_option( 'afrfq_site_key' ) ); ?>"></div>
						</div>
					<?php } ?>
				<?php } ?>

				<div class="form_row">
					<input name="afrfq_action" type="hidden" value="save_afrfq"/>

					<?php
					$afrfq_submit_button_text        = get_option( 'afrfq_submit_button_text' );
					$afrfq_submit_button_bg_color    = get_option( 'afrfq_submit_button_bg_color' );
					$afrfq_submit_button_fg_color    = get_option( 'afrfq_submit_button_fg_color' );
					$afrfq_enable_qoute_button_color = get_option( 'afrfq_enable_qoute_button_color' );
					$afrfq_submit_button_text        = empty( $afrfq_submit_button_text ) ? __( 'Place Quote', 'addify_b2b' ) : $afrfq_submit_button_text;
					?>

					<style type="text/css">
						.addify_checkout_place_quote{
							<?php if ('yes' == $afrfq_enable_qoute_button_color) { ?>
							color: <?php echo esc_html( $afrfq_submit_button_fg_color ); ?> !important;
							background-color: <?php echo esc_html( $afrfq_submit_button_bg_color ); ?> !important;
						<?php } ?>
						}

					</style>
					<?php wp_nonce_field( 'save_afrfq', 'afrfq_nonce' ); ?>
					
					<button type="submit" name="addify_checkout_place_quote" class="button alt addify_checkout_place_quote"><?php echo esc_html( $afrfq_submit_button_text ); ?></button>
				</div>
				</div>
			</div>
		</form>
		
	</div>

<?php } else { ?>

	<div class="addify">
		<p class="cart-empty"><?php echo esc_html__( 'Your quote is currently empty.', 'addify_b2b' ); ?></p>
				<p class="return-to-shop"><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="button wc-backward"><?php echo esc_html__( 'Return To Shop', 'addify_b2b' ); ?></a>
		</p>
	</div>
	
	<?php
}
?>
</div>
