<?php
require AFB2B_PLUGIN_DIR . 'vendor/autoload.php';
require_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-quote-fields.php';
use Dompdf\Dompdf;
use Dompdf\Options;

function af_rfq_gernate_pdf_of_order(
	$af_rfq_is_email_send_to_admin,
	$af_rfq_is_email_send_to_user,
	$is_ajax_applied,
	$afrfq_is_multiple_quotes_downloaded,
	$admin_add_new_qoute,
	$customer_add_new_qoute,
	$is_qoute_status_update,
	$qoute_id_arr = array()
) {
	
	$quote_fields_obj         = new AF_R_F_Q_Quote_Fields();
	$quote_fields             = (array) $quote_fields_obj->quote_fields;
	$afrfq_admin_defaut_email = get_option( 'admin_email' );
				
	if (!empty(get_option( 'afrfq_backrgound_color' ))) {
		$afrfq_backrgound_color = get_option( 'afrfq_backrgound_color' );
	} else {
		$afrfq_backrgound_color ='#d6e4f1';
	}
	if (!empty(get_option( 'afrfq_text_color_for_background' ))) {
		$afrfq_text_color_for_background = get_option( 'afrfq_text_color_for_background' );
	} else {
		$afrfq_text_color_for_background ='#000';   
	}

	$af_rfq_privicy_and_term_conditions =get_option( 'afrfq_term_and_condition_text' );
			
	if (!empty($af_rfq_privicy_and_term_conditions)) {
		$af_rfq_privicy_and_term_conditions =get_option( 'afrfq_term_and_condition_text' );    
	} else {
		$af_rfq_privicy_and_term_conditions ='Privacy & Terms and Conditions';
	}

	$afrfq_enable_border = get_option('afrfq_enable_border');
	$afrfq_border_color  = !empty(get_option('afrfq_border_color')) ? get_option('afrfq_border_color') : '#244F70';

	$size                       = 'woocommerce_thumbnail';
	$afrfq_default_image        = wc_placeholder_img_src( $size );
	$af_rfq_pdf_layout_selected = get_option( 'afrfq_pdf_select_layout' );
	$af_rfq_get_site_title      = get_bloginfo('name');
	$af_rfq_get_site_address_1  = get_option('woocommerce_store_address');
	$af_rfq_get_site_address_2  = get_option('woocommerce_store_address_2');

	//common data of all pdf templates
	$options = new Options();
	$options->set( 'isRemoteEnabled', true );
	$dompdf = new Dompdf( $options );

	$email_values = (array) get_option( 'afrfq_emails' );
	$user_id      = get_post_meta( current( $qoute_id_arr ), '_customer_user', true );
	$user_name    = 'Guest';
	$id_user      = 'guest';
	$user         = get_user_by( 'id', intval( $user_id ) );
	
	if ( ! empty( $user_id ) && is_object( $user ) ) {
		$user_name         = $user->display_name;
		$af_rfq_user_email = $user->user_email;

	}

	if ( ! isset( $afrfq_quote_details ) ) {
		$afrfq_quote_details = new AF_R_F_Q_Quote();
	}

	if ( ! empty( get_option( 'afrfq_admin_email' ) ) ) {
		$admin_email = get_option( 'afrfq_admin_email' );
	} else {
		$admin_email = $afrfq_admin_defaut_email;
	}

	$afrfq_admin_email_pdf = get_option( 'afrfq_admin_email_pdf' );

	$afrfq_company_name             = get_option( 'afrfq_company_name_text' );
	$afrfq_user_email_pdf           = get_option( 'afrfq_user_email_pdf' );

	switch ( $af_rfq_pdf_layout_selected ) {
		case 'afrfq_template3':
			ob_start();
			?>
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset="UTF-8">
				</head>
				<style>
					dt{
						font-weight: bold;
						font-size: 10px !important;

					}

					dd{
						margin: 0px;
						font-size: 10px !important;

					}
					dt p,
					dd p{
						margin:4px 0px;
					}
				</style>
				<body>
				
				<div id="addify_quote_items_container" style="font-family: sans-serif!important;">
				
			<?php

			foreach ( $qoute_id_arr as $qoute_id ) {

				$quote_contents = get_post_meta( $qoute_id, 'quote_contents', true );

				$afrfq_get_qoute_by_id = get_post( $qoute_id );

				$quote_status       = get_post_meta( $qoute_id, 'quote_status', true );
				$afrfq_quote_totals = $afrfq_quote_details->get_calculated_totals( $quote_contents, $qoute_id );

				$status                         = str_replace('af_', '', $quote_status); // Remove af_ prefix
				$status = str_replace('_', ' ', $status); // Replace underscores with spaces
				$afrfq_get_qoute_status_for_pdf =  esc_attr( ucwords($status) );


				$shipping_cost = get_post_meta( $qoute_id, 'afrfq_shipping_cost', true );

				// enable price display
				$allowed_user_roles = get_option('afrfq_enable_for_specific_user_role');

				$user_id        = get_post_meta( $qoute_id, '_customer_user', true );
				$user           = ! empty( $user_id ) ? get_user_by( 'id', intval( $user_id ) ) : null;
				$user_role      = is_object( $user ) ? $user->roles : array( 'guest' );

				$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
				$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
				$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
		


				?>
					<div class="afrfq_info_div">
					   
					<div class="af_rfq_company_information " style="display:inline-block; width:63%; vertical-align:top;">
						<div class="afrfq_company_logo_preview">
						<?php if ( get_option( 'afrfq_company_logo' ) ) : ?>
								<img src="<?php echo esc_url( get_option( 'afrfq_company_logo' ) ); ?>" alt="Company Logo" style="width: 90px;" />
							<?php endif; ?>
							<?php if (!empty($afrfq_company_name)) { ?>
								<h3 class="customer-logo" style="margin-bottom:0; font-size: 16px; color:#000;"><?php echo esc_html($afrfq_company_name); ?></h3>
							<?php } ?>
						</div>
						<div class="afrfq_company_info_sub_details" style="font-size:12px; line-height:12px;">

						<?php if (!empty(get_option( 'afrfq_company_address' ))) { ?>
							<p><strong style="margin-right: 12px;"><?php esc_html_e( 'Address:', 'addify_b2b' ); ?></strong><?php echo esc_attr( get_option( 'afrfq_company_address' ) ); ?></p>
						<?php } elseif (!empty($af_rfq_get_site_address_1)) { ?>
							<p><strong style="margin-right: 12px;"><?php esc_html_e( 'Address:', 'addify_b2b' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_1 ); ?></p>
						<?php } elseif (( !empty($af_rfq_get_site_address_2) )&&( empty($af_rfq_get_site_address_1) )) { ?>
							<p><strong style="margin-right: 12px;"><?php esc_html_e( 'Address:', 'addify_b2b' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_2 ); ?></p>
						<?php } ?>
						<p><strong style="margin-right: 12px;"><?php esc_html_e( 'Email:', 'addify_b2b' ); ?></strong>
						<?php
							$af_rfq_email_array_pdf = explode( ',', $admin_email );
							$iteration              = 1; // Initialize an iteration counter.

						foreach ( $af_rfq_email_array_pdf as $email ) {
							if ( $iteration > 1 ) {
								// Apply margin-left to the email address starting from the second iteration.
								echo '<p style="font-size:12px; line-height:12px; margin-left:54px;">' . esc_attr( $email ) . '</p>';
							} else {
								echo esc_attr( $email ); // No margin-left for the first iteration.
							}
							++$iteration; // Increment the iteration counter.
						}
						?>
							</p>
						</div>
					</div>
			
					<div class="afrfq-quote-detail" style="display:inline-block;  vertical-align:top; width:35%;">
						<div style="background:<?php echo esc_attr( $afrfq_backrgound_color ); ?>;padding: 10px 10px; margin-bottom:18px;">
						<h1 style="font-size:16px; line-height:16px; margin:0; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>;"><?php esc_html_e( 'Quote', 'addify_b2b' ); ?></h1>
						</div>
						
						<p style="font-size:12px; "><strong style="display:inline-block; width:100px;"><?php esc_html_e( 'Quote ID:', 'addify_b2b' ); ?></strong><span><?php echo esc_attr( $qoute_id ); ?></span></p>
						<p style="font-size:12px;"><strong style="display:inline-block; width:100px;"><?php esc_html_e( 'Quote Status:', 'addify_b2b' ); ?></strong><span><?php echo esc_attr( $afrfq_get_qoute_status_for_pdf ); ?></span></p>
						<p style="font-size:12px;"><strong style="display:inline-block; width:100px;"><?php esc_html_e( 'Quote Date:', 'addify_b2b' ); ?></strong><span><?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $afrfq_get_qoute_by_id->post_date ) ) ); ?></span></p>
					</div>
					<div class="afrfq_client_info_details" style="vertical-align:top;width:50%; margin-top: -10px;">
						<h1 class="cust_info_text" style="margin-bottom:18px; padding: 10px 10px;  background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px; line-height:16px;" ><?php esc_html_e( 'Customer Information', 'addify_b2b' ); ?></h1>
						<?php
						$afrfq_increment_j = 0;
						foreach ( $quote_fields as $key => $field ) {
							$field_id          = $field->ID;
							$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
							$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );
							$afrfq_field_value = get_post_meta( $field_id, 'afrfq_field_value', true );
							$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
							$field_data        = get_post_meta( $qoute_id, $afrfq_field_name, true );

							if ( empty( $field_data ) && ! empty( $afrfq_field_value ) ) {
								$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
							}

							if ( ( ! empty( $field_data ) ) && ( 'terms_cond' != $afrfq_field_type ) ) {
								++$afrfq_increment_j;

								if ( is_array( $field_data ) ) {

									$field_data = implode( ',', $field_data );
								}

								if ( empty( $afrfq_field_label ) ) {
									if ( 'user_login' == $afrfq_field_value ) {
										$afrfq_field_value = 'Username';
										?>
										<p  style="font-size:12px;" ><strong style="margin-right:8px; font-size:12px; line-height: 13px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
										<?php
									} else {
										$afrfq_field_value = ucwords( str_replace( '_', ' ', $afrfq_field_value ) );
										?>
										<p  style="font-size:12px;" ><strong style="margin-right:8px; font-size:12px; line-height: 13px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
										<?php
									}
								} else {
									?>
									<p  style="font-size:12px; line-height: 13px;" ><strong style="margin-right:8px;"><?php echo esc_attr( $afrfq_field_label . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
									<?php
								}
							}
						}
						if ( 0 == $afrfq_increment_j ) {
							?>
							<style>
								.cust_info_text{
									display:none;
								}
							</style>
						<?php
						}
						?>
					</div>
				   
					</div>
					<table cellpadding="0" cellspacing="0" style="margin-top:7px; border-collapse: collapse; width:100%; border-bottom:1px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
						<thead style="background:<?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
				
						<tr style="border-bottom:1px solid #d3d3d3a3;">
						<th style="padding:12px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px;" class="thumb sortable" data-sort="string-ins"><?php esc_html_e( 'Image', 'addify_b2b' ); ?></th>
								<th style="padding:15px 8px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px; text-align: left; width:30%;" class="item sortable" data-sort="string-ins"><?php esc_html_e( 'Product', 'addify_b2b' ); ?></th>
								<?php if ( $price_display ) { ?>
										<th style="padding:15px 8px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px; min-width: 75px" class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Price', 'addify_b2b' ); ?></th>
									<?php } ?>
								<?php if ( $of_price_display ) { ?>
									<th style="padding:15px 8px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px; min-width: 90px" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered price', 'addify_b2b' ); ?></th>
									<?php } ?>
								<th style="padding:15px 8px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Quantity', 'addify_b2b' ); ?></th>
								<?php if ( $price_display ) { ?>
										<th style="padding:15px 8px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px;" class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Subtotal', 'addify_b2b' ); ?></th>
									<?php } ?>
								<?php if ( $of_price_display ) { ?>
									<th style="padding:15px 8px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px; min-width: 110px" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered Subtotal', 'addify_b2b' ); ?></th>
									<?php } ?>
								</tr>
							<tr>                              
						</tr>
						</thead>
						<tbody>
						<?php

						$offered_price_subtotal = 0;

						foreach ( (array) $quote_contents as $item_id => $item ) {
							if ( isset( $item['data'] ) ) {
								$product = $item['data'];
							} else {
								continue;
							}

							if ( ! is_object( $product ) ) {
								continue;
							}

							// incase product type is custom and now the plugin which registers that product type is not active then it simply empties the quote
							// this case is mainly added to tackle composite product in quote when configurable product plugin is not active
							if (is_object($product) && $product instanceof __PHP_Incomplete_Class) {
								continue;
							}

							$price                   = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
							$price                   = empty( $item['role_base_price'] ) ? $price : $item['role_base_price'];
							$price                   = empty( $item['price_calculator_price'] ) ? $price : $item['price_calculator_price'];
							$price                   = isset($item['composite_product_price']) ? $item['composite_product_price'] : $price;
							$qty_display             = $item['quantity'];
							$offered_price           = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;
							$product_link            = $product ? admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) : '';
							$thumbnail               = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
							$offered_price_subtotal += floatval( $offered_price ) * intval( $qty_display );
							$image                   = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
							$image_url               = isset( $image[0] ) ? $image[0] : '';

							$component_name = isset($item['component_name']) ? $item['component_name'] . '<br><br>' : '';

							?>
								<tr class="item" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
									<td class="thumb" style="">
									<?php if ( ! empty( $image_url ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $image_url ); ?>" alt="Product Image"/>
											<?php } elseif ( ( empty( $image_url ) ) && ( extension_loaded( 'gd' ) ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $afrfq_default_image ); ?>" alt="Product Image"/>				
											<?php } ?>
									</td>
									<td class="woocommerce-table__product-name product-name" style=" padding: 15px 8px; text-align:left; font-size: 12px; color:#000; width:30%;overflow-wrap: anywhere; word-break: break-word; ">
									<?php
									$product_permalink = get_edit_post_link( $product->get_id() );
									echo wp_kses_post($component_name);
									echo wp_kses_post( apply_filters( 'addify_rfq_order_item_name', $product_permalink ? sprintf( '<a href="%s" style="color:#000; text-decoration:none;">%s</a>', $product_permalink, $product->get_name() ) : $product->get_name(), $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped									
									if (!empty($product->get_sku())) {
										echo wp_kses_post( '<div class="wc-quote-item-sku" style="font-size:10px; margin-top:10px;"><strong style="font-size:10px;">' . esc_html__( 'SKU:', 'addify_b2b' ) . '</strong> ' . esc_attr( $product->get_sku() ) . '</div>' );
									}
									echo '<span style="font-family:DejaVu Sans">' . wp_kses_post( wc_get_formatted_cart_item_data( $item ) ) . '</span>';
									?>
									</td>
									<?php if ( $price_display ) { ?>
									<td class="woocommerce-table__product-total product-total" style="font-family:DejaVu Sans; padding: 15px 8px; text-align:center; color:#000; font-size: 12px;">
										<?php echo wp_kses_post( wc_price( $price ) ); ?>
									</td>
									<?php } ?>
									<?php if ( $of_price_display && ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) )) { ?>
									<td style="font-family:DejaVu Sans; padding: 15px 8px; text-align:center; color:#000; font-size: 12px;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
										<?php echo wp_kses_post( wc_price( $offered_price ) ); ?>
									</td>
									<?php
									} else if ($of_price_display) {
										?>
										<td></td>
										<?php
									}
									?>
																		<td style="padding: 15px 8px; text-align:center; font-size: 12px; color:#000;">
										<?php echo esc_attr( $item['quantity'] ); ?>
									</td>
									<?php if ( $price_display && ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) )) { ?>
									<td class="woocommerce-table__product-total product-total" style="font-family:DejaVu Sans; padding: 15px 20x; text-align:center; color:#000; font-size: 12px; width: 15%;">
										<?php echo wp_kses_post( wc_price( $price * $qty_display ) ); ?>
									</td>
									<?php
									} else if ($price_display) {
										?>
										<td></td>
										<?php
									}
									?>
									<?php if ( $of_price_display && ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) ) ) { ?>
									<td style="font-family:DejaVu Sans; padding: 15px 8px; text-align:center; color:#000; font-size: 12px;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
										<?php echo wp_kses_post( wc_price( $offered_price * $qty_display ) ); ?>
									</td>
									<?php
									} else if ($of_price_display) {
										?>
										<td></td>
										<?php
									}
									?>
								</tr>
								<?php } ?>
							</tbody>
							</table>
					<div>
					
					<table style="width: 40%; margin-left: auto;">
						<tbody class="adf-pdf-total-amount">
					
								<?php

								if ( isset( $afrfq_quote_totals['_subtotal'] ) && ( $price_display ) ) {
									?>
									<tr style="border-top:1px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>; margin-top:15px; font-family: sans-serif;">
										<td style="padding: 15px 5px 5px; color:#000; font-size: 12px; text-align:left; font-weight:bold; font-family:sans-serif;" ><?php esc_html_e( 'Subtotal(Standard)', 'addify_b2b' ); ?></td>
										<td style="padding: 15px 5px 5px; color:#000; font-size: 12px; text-align:right; font-family:DejaVu Sans;" ><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_subtotal'] ) ); ?></td> 
									</tr>
									<?php
								}

								if ( isset( $afrfq_quote_totals['_offered_total'] ) && ( $of_price_display ) ) {
									?>
									<tr>
										<td style="padding:5px; color:#000; font-size: 12px; text-align:left; font-weight:bold; font-family:sans-serif;"><?php esc_html_e( 'Offered Subtotal', 'addify_b2b' ); ?></td>
										<td style="padding:5px; color:#000; font-size: 12px; text-align:right;font-family:DejaVu Sans;" ><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_offered_total'] ) ); ?></td> 
									</tr>
					
									<?php
								}

								if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_tax_total'] ) && ( ( $price_display ) && $tax_display ) ) {
									?>
									<tr>
										<td style="padding:5; color:#000; font-size: 12px; text-align:left; font-weight:bold; font-family:sans-serif;" ><?php esc_html_e( 'VAT(Standard)', 'addify_b2b' ); ?></td>
										<td style="padding:5px; color:#000; font-size: 12px; text-align:right;font-family:DejaVu Sans;" ><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_tax_total'] ) ); ?></td> 
									</tr>
									<?php
								}

								if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_offered_tax_total'] ) && ( ( $of_price_display ) && $tax_display ) ) {
									?>
									<tr>
										<td style="padding:5; color:#000; font-size: 12px; text-align:left; font-weight:bold; font-family:sans-serif;" ><?php esc_html_e( 'Offered VAT', 'addify_b2b' ); ?></td>
										<td style="padding:5px; color:#000; font-size: 12px; text-align:right;font-family:DejaVu Sans;" ><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_offered_tax_total'] ) ); ?></td> 
									</tr>
									<?php
								}

								if ( $shipping_cost && ( ( $price_display || $of_price_display ) ) ) {
									?>
									<tr>
										<td style="padding:5; color:#000; font-size: 12px; text-align:left; font-weight:bold; font-family:sans-serif;" ><?php esc_html_e( 'Shipping Cost', 'addify_b2b' ); ?></td>
										<td style="padding:5px; color:#000; font-size: 12px; text-align:right;font-family:DejaVu Sans;" ><?php echo wp_kses_post( wc_price( $shipping_cost ) ); ?></td> 
									</tr>
									<?php
								}


								if ( isset( $afrfq_quote_totals['_total'] ) && ( $price_display ) ) {
									?>
									<tr>
										<td style="padding: 5px; color:#000; font-size: 12px; text-align:left;  font-weight:bold; font-family:sans-serif;"><?php esc_html_e( 'Total(Standard)', 'addify_b2b' ); ?></td>
										<td style="padding: 5px; font-weight:bold; text-align:right; font-size: 12px;font-family:DejaVu Sans;"><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_total'] ) ); ?></td> 
									</tr>
									<?php
								}

								if ( isset( $afrfq_quote_totals['_offered_total_after_tax'] ) && ( $of_price_display ) ) {
									?>
									<tr>
										<td style="padding: 5px; color:#000; font-size: 12px; text-align:left;  font-weight:bold; font-family:sans-serif;"><?php esc_html_e( 'Offered Total', 'addify_b2b' ); ?></td>
										<td style="padding: 5px; font-weight:bold; text-align:right;font-size: 12px;font-family:DejaVu Sans;"><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_offered_total_after_tax'] ) ); ?></td> 
									</tr>
									<?php
								}

								?>
								</tbody>
					</table>
					<?php if ( 'yes' == get_option( 'afrfq_enable_term_and_condition' ) ) { ?>
							<div class="afrfq_client_info_details" style="font-family:sans-serif;">
							<h1 style="font-family:sans-serif; margin-bottom:10px; padding:10px; font-size:18px; line-height:28px;background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>" ><?php esc_html_e( 'Terms & Conditions', 'addify_b2b' ); ?></h1>
							<ul style="font-family:sans-serif; margin:0px; padding:0 15px 15px; font-size: 14px; line-height:23px" ><li><?php echo esc_attr( $af_rfq_privicy_and_term_conditions ); ?></li></ul>
						</div>
						<?php } ?>
				</div>
			
				<?php } ?>
				</div>
				</body>
				</html>
				<?php

				$html = ob_get_clean();
				//  echo wp_kses_post( $html );
				// exit;
				$dompdf->loadHtml( $html );
				$dompdf->setPaper( 'A4', 'portrait' );
				$dompdf->render();
				$pdf_content   = $dompdf->output();
				$pdf_file_name = 'Quote_' . current( $qoute_id_arr ) . '.pdf';

				$file_to_save = AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf';
				file_put_contents( $file_to_save, $pdf_content );

				if ( ( true == $afrfq_is_multiple_quotes_downloaded )
				&& ( ( 'yes' == $afrfq_admin_email_pdf ) ) ) {

					$to          = $admin_email;
					$subject     = 'Your PDF';
					$message     = 'Here is the PDF you requested.';
					$headers     = array(
						'Content-Type: text/html; charset=UTF-8',
					);
					$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
					wp_mail( $to, $subject, $message, $headers, $attachments );

				} elseif ( ( true == $is_ajax_applied ) && ( true == $is_qoute_status_update ) ) {

					$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );

					return $attachments;

				} elseif ( ( true == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) {

					if ( isset( $email_values['af_admin']['enable'] ) && ( 'yes' === $email_values['af_admin']['enable'] ) && ( ! empty( $email_values['af_admin']['subject'] ) ) ) {

						$subject            = $email_values['af_admin']['subject'] ? $email_values['af_admin']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin']['heading'] ? $email_values['af_admin']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin']['message'] ? $email_values['af_admin']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_pending' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_pending']['enable'] ) && ( 'yes' === $email_values['af_pending']['enable'] ) && ( ! empty( $email_values['af_pending']['subject'] ) ) ) {

						$subject            = $email_values['af_pending']['subject'] ? $email_values['af_pending']['subject'] : 'Your PDF';
						$heading            = $email_values['af_pending']['heading'] ? $email_values['af_pending']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_pending']['message'] ? $email_values['af_pending']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_in_process' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_in_process']['enable'] ) && ( 'yes' === $email_values['af_in_process']['enable'] ) && ( ! empty( $email_values['af_in_process']['subject'] ) ) ) {

						$subject            = $email_values['af_in_process']['subject'] ? $email_values['af_in_process']['subject'] : 'Your PDF';
						$heading            = $email_values['af_in_process']['heading'] ? $email_values['af_in_process']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_in_process']['message'] ? $email_values['af_in_process']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_accepted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_accepted']['enable'] ) && ( 'yes' === $email_values['af_accepted']['enable'] ) && ( ! empty( $email_values['af_accepted']['subject'] ) ) ) {

						$subject            = $email_values['af_accepted']['subject'] ? $email_values['af_accepted']['subject'] : 'Your PDF';
						$heading            = $email_values['af_accepted']['heading'] ? $email_values['af_accepted']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_accepted']['message'] ? $email_values['af_accepted']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_converted_to_cart' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_converted_to_cart']['enable'] ) && ( 'yes' === $email_values['af_converted_to_cart']['enable'] ) && ( ! empty( $email_values['af_converted_to_cart']['subject'] ) ) ) {

						$subject            = $email_values['af_converted_to_cart']['subject'] ? $email_values['af_converted_to_cart']['subject'] : 'Your PDF';
						$heading            = $email_values['af_converted_to_cart']['heading'] ? $email_values['af_converted_to_cart']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_converted_to_cart']['message'] ? $email_values['af_converted_to_cart']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}

					if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

						$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_converted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_converted']['enable'] ) && ( 'yes' === $email_values['af_converted']['enable'] ) && ( ! empty( $email_values['af_converted']['subject'] ) ) ) {

						$subject            = $email_values['af_converted']['subject'] ? $email_values['af_converted']['subject'] : 'Your PDF';
						$heading            = $email_values['af_converted']['heading'] ? $email_values['af_converted']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_converted']['message'] ? $email_values['af_converted']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}

					if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

						$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_declined' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_declined']['enable'] ) && ( 'yes' === $email_values['af_declined']['enable'] ) && ( ! empty( $email_values['af_declined']['subject'] ) ) ) {

						$subject            = $email_values['af_declined']['subject'] ? $email_values['af_declined']['subject'] : 'Your PDF';
						$heading            = $email_values['af_declined']['heading'] ? $email_values['af_declined']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_declined']['message'] ? $email_values['af_declined']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_cancelled' === $quote_status ) && ( false == $admin_add_new_qoute ) ) {

					if ( isset( $email_values['af_cancelled']['enable'] ) && ( 'yes' === $email_values['af_cancelled']['enable'] ) && ( ! empty( $email_values['af_cancelled']['subject'] ) ) ) {
						$subject            = $email_values['af_cancelled']['subject'] ? $email_values['af_cancelled']['subject'] : 'Your PDF';
						$heading            = $email_values['af_cancelled']['heading'] ? $email_values['af_cancelled']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_cancelled']['message'] ? $email_values['af_cancelled']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				}

				if ( true == $is_ajax_applied ) {

					$file_to_save_via_url = AFRFQ_URL . 'includes/pdf/pdf-files/Quotes.pdf';
					return $file_to_save_via_url;

				} else {

					$dompdf->stream('Quote.pdf');
					exit( 0 );
				}

			break;

		case 'afrfq_template2':
			ob_start();
			?>
					<!DOCTYPE html>
					<html>
					<head>
						<meta charset="UTF-8">
					</head>
						<style>
							dt{
								font-weight: bold;
								font-size: 10px !important;
							}

							dd{
								margin: 0px;
								font-size: 10px !important;
							}
							dt p,
							dd p{
								margin:4px 0px;
							}
						</style>
					<body>
					
					<div id="addify_quote_items_container" style="font-family: sans-serif!important;">
					
				<?php

				foreach ( $qoute_id_arr as $qoute_id ) {

					$quote_contents = get_post_meta( $qoute_id, 'quote_contents', true );

					$afrfq_get_qoute_by_id = get_post( $qoute_id );

					$quote_status       = get_post_meta( $qoute_id, 'quote_status', true );
					$afrfq_quote_totals = $afrfq_quote_details->get_calculated_totals( $quote_contents, $qoute_id );

					$status                         = str_replace('af_', '', $quote_status); // Remove af_ prefix
					$status = str_replace('_', ' ', $status); // Replace underscores with spaces
					$afrfq_get_qoute_status_for_pdf =  esc_attr( ucwords($status) );

					$shipping_cost = get_post_meta( $qoute_id, 'afrfq_shipping_cost', true );

					// enable price display
					$allowed_user_roles = get_option('afrfq_enable_for_specific_user_role');

					$user_id        = get_post_meta( $qoute_id, '_customer_user', true );
					$user           = ! empty( $user_id ) ? get_user_by( 'id', intval( $user_id ) ) : null;
					$user_role      = is_object( $user ) ? $user->roles : array( 'guest' );

					$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
					$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
					$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;

					?>
					<div class="afrfq_info_div" style="display:flex;">
						   
						<div class="af_rfq_company_information " style="display:block; vertical-align:top;">
							<div class="Qoute" style="font-size:22px; display:inline-block;width:70%; line-height:32px; vertical-align:top;" >
								<?php 
								if (!empty(get_option( 'afrfq_company_name_text' ))) {
									$afrfq_company_name = get_option( 'afrfq_company_name_text' ); 
									?>
									<h1 style="font-size:19px; margin-top:0; margin-bottom: 0;"><?php echo esc_attr( $afrfq_company_name ); ?></h1>
								<?php } elseif (!empty($af_rfq_get_site_title)) { ?>
									<h1 style="font-size:19px; margin-top:0; margin-bottom: 0;"><?php echo esc_attr($af_rfq_get_site_title); ?></h1>
								<?php } ?>
								<div class="afrfq_company_info_sub_details" style="font-size:12px; line-height: 13px;">
									<?php if (!empty(get_option( 'afrfq_company_address' ))) { ?>
									<p><?php echo esc_attr( get_option( 'afrfq_company_address' ) ); ?></p>
								<?php } elseif (!empty($af_rfq_get_site_address_1)) { ?>
									<p><?php echo esc_attr( $af_rfq_get_site_address_1 ); ?></p>
								<?php } elseif (( !empty($af_rfq_get_site_address_2) )&&( empty($af_rfq_get_site_address_1) )) { ?>
									<p><?php echo esc_attr( $af_rfq_get_site_address_2 ); ?></p>
								<?php } ?>
										<p>
									<?php
										$af_rfq_email_array_pdf                = explode( ',', $admin_email );
																	$iteration = 1; // Initialize an iteration counter.

									foreach ( $af_rfq_email_array_pdf as $email ) {
										if ( $iteration > 1 ) {
											// Apply margin-left to the email address starting from the second iteration.
											echo '<p style="font-size:12px; line-height: 13px;"">' . esc_attr( $email ) . '</p>';
										} else {
											echo esc_attr( $email ) . ''; // No margin-left for the first iteration.
										}
										++$iteration; // Increment the iteration counter.
									}
									?>
									</p>
									</div>
								</div>
								<div class="afrfq_company_logo_preview" style="text-align:right; display:inline-block;width:25%;vertical-align:top;">
									<?php if ( get_option( 'afrfq_company_logo' ) ) : ?>
										<img src="<?php echo esc_url( get_option( 'afrfq_company_logo' ) ); ?>" alt="Company Logo" style="width: 80px;" />
									<?php endif; ?>
								</div>
							</div>
	
						<div class="afrfq_client_info_details" style="vertical-align:top; width: 48%; display: inline-block;">
							<h1 style="margin-bottom:20px;padding:12px;  background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px; line-height: 16px; font-weight: 700;" ><?php esc_html_e( 'Customer Information', 'addify_b2b' ); ?></h1>
							<?php
										$afrfq_increment_j = 0;
							foreach ( $quote_fields as $key => $field ) {
								$field_id          = $field->ID;
								$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
								$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );
								$afrfq_field_value = get_post_meta( $field_id, 'afrfq_field_value', true );
								$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
								$field_data        = get_post_meta( $qoute_id, $afrfq_field_name, true );

								if ( empty( $field_data ) && ! empty( $afrfq_field_value ) ) {
									$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
								}

								if ( ( ! empty( $field_data ) ) && ( 'terms_cond' != $afrfq_field_type ) ) {
									++$afrfq_increment_j;

									if ( is_array( $field_data ) ) {
										$field_data = implode( ',', $field_data );
									}

									if ( empty( $afrfq_field_label ) ) {
										if ( 'user_login' == $afrfq_field_value ) {
											$afrfq_field_value = 'Username';
											?>
											<p  style="font-size:12px; line-height:13px;" ><strong style="margin-right:8px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
											<?php
										} else {
											$afrfq_field_value = ucwords( str_replace( '_', ' ', $afrfq_field_value ) );
											?>
											<p  style="font-size:12px; line-height:13px;" ><strong style="margin-right:8px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
											<?php

										}
									} else {
										?>
										<p  style="font-size:12px;" ><strong style="margin-right:8px; font-size:12px;"><?php echo esc_attr( $afrfq_field_label . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
										<?php
									}
								}
							}
							if ( 0 == $afrfq_increment_j ) {
								?>
								<style>
									.cust_info_text{
										display:none;
									}
								</style>
							<?php
							}
							?>
						</div>
						<div class="afrfq-quote-detail" style=" width:48%; display:inline-block; vertical-align:top; margin-left: 3%;">
								<h1 style="margin-bottom:20px;line-height:20px; padding:12px;  background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px; line-height: 16px; font-weight: 700;" ><?php esc_html_e( 'Quote', 'addify_b2b' ); ?></h1>
								<p style="font-size:12px; line-height:13px;"><strong style="display:inline-block; width:100px;"><?php esc_html_e( 'Quote ID:', 'addify_b2b' ); ?></strong><span><?php echo esc_attr( $qoute_id ); ?></span></p>
								<p style="font-size:12px; line-height:13px;"><strong style="display:inline-block; width:100px;"><?php esc_html_e( 'Quote Status:', 'addify_b2b' ); ?></strong><span><?php echo esc_attr( $afrfq_get_qoute_status_for_pdf ); ?></span></p>
								<p style="font-size:12px; line-height:13px;"><strong style="display:inline-block; width:100px;"><?php esc_html_e('Quote Date:', 'addify_b2b' ); ?></strong><span><?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $afrfq_get_qoute_by_id->post_date ) ) ); ?></span></p>
							</div>
						</div>
						<table cellpadding="0" cellspacing="0" style="margin-top:10px; border-collapse: collapse; width:100%;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
							<thead style="background:<?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
								<tr >
									<th style="padding:12px 8px; font-size:12px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px;" class="thumb sortable" data-sort="string-ins"><?php esc_html_e( 'Image', 'addify_b2b' ); ?></th>
									<th style="padding:12px 8px; font-size:12px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px; text-align: left; width:35%;" class="item sortable" data-sort="string-ins"><?php esc_html_e( 'Product', 'addify_b2b' ); ?></th>
								<?php if ( $price_display ) { ?>
									<th style="padding:12px 8px; font-size:12px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px; width: 70px;" class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Price', 'addify_b2b' ); ?></th>
									<?php } ?>
								<?php if ( $of_price_display ) { ?>
									<th style="padding:12px 8px; font-size:12px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px; width:90px;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered price', 'addify_b2b' ); ?></th>
									<?php } ?>
								<th style="padding:12px 8px; font-size:12px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Quantity', 'addify_b2b' ); ?></th>
								<?php if ( $price_display ) { ?>
									<th style="padding:12px 8px; font-size:12px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px;width:90px;"  class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Subtotal', 'addify_b2b' ); ?></th>
								<?php } ?>
								<?php if ( $of_price_display ) { ?>
									<th style="padding:12px 8px; font-size:12px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 12px; width:110px;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered Subtotal', 'addify_b2b' ); ?></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
							<?php

							$offered_price_subtotal = 0;

							foreach ( (array) $quote_contents as $item_id => $item ) {
								if ( isset( $item['data'] ) ) {
									$product = $item['data'];
								} else {
									continue;
								}

								if ( ! is_object( $product ) ) {
									continue;
								}

								// incase product type is custom and now the plugin which registers that product type is not active then it simply empties the quote
								// this case is mainly added to tackle composite product in quote when configurable product plugin is not active
								if (is_object($product) && $product instanceof __PHP_Incomplete_Class) {
									continue;
								}

								$price                   = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
								$price                   = empty( $item['role_base_price'] ) ? $price : $item['role_base_price'];
								$price                   = empty( $item['price_calculator_price'] ) ? $price : $item['price_calculator_price'];
								$price                   = isset($item['composite_product_price']) ? $item['composite_product_price'] : $price;

								$qty_display             = $item['quantity'];
								$offered_price           = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;
								$product_link            = $product ? admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) : '';
								$thumbnail               = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
								$offered_price_subtotal += floatval( $offered_price ) * intval( $qty_display );
								$image                   = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
								$image_url               = isset( $image[0] ) ? $image[0] : '';

								$component_name = isset($item['component_name']) ? $item['component_name'] . '<br><br>' : '';

								?>
									<tr class="item" data-order_item_id="<?php echo esc_attr( $item_id ); ?>"  style="border-bottom:1px solid #d3d3d3a3;">
										<td class="thumb" style="padding: 15px 20x; text-align:center;">
										<?php if ( ! empty( $image_url ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $image_url ); ?>" alt="Product Image"/>
											<?php } elseif ( ( empty( $image_url ) ) && ( extension_loaded( 'gd' ) ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $afrfq_default_image ); ?>" alt="Product Image"/>				
											<?php } ?>
										</td>
										
										<td class="woocommerce-table__product-name product-name" style=" padding:15px 8px!important; text-align:left; font-size: 12px; color:#000; width:35%; overflow-wrap: anywhere; word-break: break-word;">
										<?php
										$product_permalink = get_edit_post_link( $product->get_id() );
										echo wp_kses_post($component_name);
										echo wp_kses_post( apply_filters( 'addify_rfq_order_item_name', $product_permalink ? sprintf( '<a href="%s" style="color:#000; text-decoration:none;">%s</a>', $product_permalink, $product->get_name() ) : $product->get_name(), $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					
										if (!empty($product->get_sku())) {
											echo wp_kses_post( '<div class="wc-quote-item-sku" style="font-size:10px; margin-top:10px;"><strong style="font-size:10px;">' . esc_html__( 'SKU:', 'addify_b2b' ) . '</strong> ' . esc_attr( $product->get_sku() ) . '</div>' );
										}
										echo '<span style="font-family:DejaVu Sans;">' . wp_kses_post( wc_get_formatted_cart_item_data( $item ) ) . '</span>';
										?>
										</td>
										<?php if ( $price_display ) { ?>
										<td class="woocommerce-table__product-total product-total" style="font-family:DejaVu Sans;padding:15px 8px; text-align:center; color:#000; font-size: 12px;">
											<?php echo wp_kses_post( wc_price( $price) ); ?>
										</td>
										<?php } ?>
										<?php if ( $of_price_display && ( ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) ) )) { ?>
											<td style="font-family:DejaVu Sans;padding:15px 8px; text-align:center; font-size: 12px; color:#000;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
												<?php echo wp_kses_post( wc_price( $offered_price ) ); ?>
											</td>
										<?php
										} else if ($of_price_display) {
											?>
											<td></td>
										<?php } ?> 
										<td style="padding:15px 8px; text-align:center; font-size: 12px; color:#000;">
											<?php echo esc_attr( $item['quantity'] ); ?>
										</td>
										<?php if ( $price_display && ( ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) ) )) { ?>
										<td class="woocommerce-table__product-total product-total" style="font-family:DejaVu Sans;padding:15px 8px; text-align:center; color:#000; font-size: 12px;">
											<?php echo wp_kses_post( wc_price( $price * $qty_display ) ); ?>
										</td>
										<?php
										} else if ($price_display) {
											?>
											<td></td>
										<?php } ?>
										<?php if ( $of_price_display && ( ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) ) )) { ?>
										<td style="font-family:DejaVu Sans;padding:15px 8px; text-align:center; font-size: 12px; color:#000;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
											<?php echo wp_kses_post( wc_price( $offered_price * $qty_display ) ); ?>
										</td>
										<?php
										} else if ($of_price_display) {
											?>
												<td></td>
											<?php } ?>
										</tr>
										<?php
							}
							?>
									</tbody>
								</table>
								<div style="width:45%; margin-left: auto;">
								<?php if ( $price_display || $of_price_display) : ?>
								<h1 style="margin-top:20px; padding:12px; font-family:sans-serif; background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:15px;  line-height: 15px; font-weight: 700;" ><?php esc_html_e( 'Quote Total', 'addify_b2b' ); ?></h1>
								<?php endif; ?>
								<table cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: sans-serif!important;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
								<tbody>
					<?php
					if ( isset( $afrfq_quote_totals['_subtotal'] ) && ( $price_display ) ) {
						?>
									<tr style="font-family: sans-serif!important;">
									<td style="padding: 10px 8px 10px 0; color:#000; font-size: 12px; text-align:left; font-weight:bold; font-family:sans-serif; " ><?php esc_html_e( 'Subtotal(Standard)', 'addify_b2b' ); ?></td>
									<?php 
									if ( ( $price_display ) && ( $of_price_display ) ) {
										?>
										<td></td>
										<td></td>
										<?php } ?>
										<td></td>
										<td></td>
										<td></td>
										<td style="font-family:DejaVu Sans;padding: 10px 8px; color:#000; font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_subtotal'] ) ); ?></td> 
									</tr>
							<?php
					}

					if ( isset( $afrfq_quote_totals['_offered_total'] ) && ( $of_price_display ) ) {
						?>
									<tr>
									<td style="padding: 10px 8px 10px 0; color:#000; font-size: 12px; text-align:left;  font-weight:bold; font-family:sans-serif;"><?php esc_html_e( 'Offered Subtotal', 'addify_b2b' ); ?></td>
							<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td style="font-family:DejaVu Sans;padding: 10px 8px; color:#000;  font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_offered_total'] ) ); ?></td> 
									</tr>
				
							<?php
					}

					if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_tax_total'] ) && ( ( $price_display ) && $tax_display ) ) {
						?>
									<tr>
									<td style="padding: 10px 8px 10px 0; color:#000; font-size: 12px; text-align:left;  font-weight:bold; font-family:sans-serif;" ><?php esc_html_e( 'VAT(Standard)', 'addify_b2b' ); ?></td>
							<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td style="font-family:DejaVu Sans;padding: 10px 8px; color:#000; font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_tax_total'] ) ); ?></td> 
									</tr>
							<?php
					}

					if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_offered_tax_total'] ) && ( ( $of_price_display ) && $tax_display ) ) {
						?>
									<tr>
									<td style="padding: 10px 8px 10px 0; color:#000; font-size: 12px; text-align:left;  font-weight:bold; font-family:sans-serif;" ><?php esc_html_e( 'Offered VAT', 'addify_b2b' ); ?></td>
							<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td style="font-family:DejaVu Sans;padding: 10px 8px; color:#000; font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price( $afrfq_quote_totals['_offered_tax_total'] ) ); ?></td> 
									</tr>
							<?php
					}


					if ( $shipping_cost && ( ( $price_display || $of_price_display ) ) ) {
						?>
									<tr>
									<td style="padding: 10px 8px 10px 0; color:#000; font-size: 12px; text-align:left;  font-weight:bold; font-family:sans-serif;" ><?php esc_html_e( 'Shipping Cost', 'addify_b2b' ); ?></td>
							<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td style="font-family:DejaVu Sans;padding: 10px 8px; color:#000; font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price( $shipping_cost ) ); ?></td> 
									</tr>
							<?php
					}

		

					if ( isset( $afrfq_quote_totals['_total'] ) && ( $price_display ) ) {
						?>
									<tr>
									<td style="padding: 10px 8px 10px 0; color:#000; font-size: 12px; text-align:left;  font-weight:600; font-family: sans-serif; border: none!important; font-weight:bold; font-family:sans-serif;"><?php esc_html_e( 'Total(Standard)', 'addify_b2b' ); ?></td>
							<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td style="border:none;"></td>
									<td style="border:none;"></td>
									<td style="font-family:DejaVu Sans;padding: 10px 8px; color:#000; font-size: 12px; text-align:right; border: none!important;">
																			<?php
																			echo wp_kses_post( wc_price( $afrfq_quote_totals['_total'] ) );

																			?>
									</td> 
									</tr>
									<?php
					}

					if ( isset( $afrfq_quote_totals['_offered_total_after_tax'] ) && ( $of_price_display ) ) {
						?>
									<tr>
									<td style="padding: 10px 8px 10px 0; color:#000; font-size: 12px; text-align:left;  font-weight:600; font-family: sans-serif; border: none!important; font-weight:bold; font-family:sans-serif;"><?php esc_html_e( 'Offered Total', 'addify_b2b' ); ?></td>
							<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td style="border:none;"></td>
									<td style="border:none;"></td>
									<td style="font-family:DejaVu Sans;padding: 10px 8px; color:#000; font-size: 12px; text-align:right; border: none!important;">
																			<?php
																			echo wp_kses_post( wc_price( $afrfq_quote_totals['_offered_total_after_tax'] ) );

																			?>
									</td> 
									</tr>
									<?php
					}

					?>
								</tbody>
								</table>
								</div>
							<?php if ( 'yes' == get_option( 'afrfq_enable_term_and_condition' ) ) { ?>
								<div class="afrfq_client_info_details" style=" margin-top:5px; vertical-align:top;  font-family:sans-serif;">
						<h1 style="margin-bottom:20px;padding:12px; font-family:sans-serif; background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px;  line-height: 16px; font-weight: 700;" ><?php esc_html_e( 'Terms & Conditions', 'addify_b2b' ); ?></h1>
						<ul  style="padding-left:13px; font-size:13px;  font-family:sans-serif; line-height: 23px;" ><li><?php echo esc_attr( $af_rfq_privicy_and_term_conditions ); ?></li></ul>
						</div>
				
				
								<?php
							}
				}
				?>
					</div>
					</body>
					</html>
				<?php

				$html = ob_get_clean();
				$dompdf->loadHtml( $html );
				$dompdf->setPaper( 'A4', 'portrait' );
				$dompdf->render();
				$pdf_content   = $dompdf->output();
				$pdf_file_name = 'Quote_' . current( $qoute_id_arr ) . '.pdf';

				$file_to_save = AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf';
				file_put_contents( $file_to_save, $pdf_content );

				if ( ( true == $afrfq_is_multiple_quotes_downloaded )
				&& ( ( 'yes' == $afrfq_admin_email_pdf ) ) ) {

					$to          = $admin_email;
					$subject     = 'Your PDF';
					$message     = 'Here is the PDF you requested.';
					$headers     = array(
						'Content-Type: text/html; charset=UTF-8',
					);
					$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
					wp_mail( $to, $subject, $message, $headers, $attachments );

				} elseif ( ( true == $is_ajax_applied ) && ( true == $is_qoute_status_update ) ) {

					$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );

					return $attachments;

				} elseif ( ( true == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) {

					if ( isset( $email_values['af_admin']['enable'] ) && ( 'yes' === $email_values['af_admin']['enable'] ) && ( ! empty( $email_values['af_admin']['subject'] ) ) ) {

						$subject            = $email_values['af_admin']['subject'] ? $email_values['af_admin']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin']['heading'] ? $email_values['af_admin']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin']['message'] ? $email_values['af_admin']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_pending' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_pending']['enable'] ) && ( 'yes' === $email_values['af_pending']['enable'] ) && ( ! empty( $email_values['af_pending']['subject'] ) ) ) {

						$subject            = $email_values['af_pending']['subject'] ? $email_values['af_pending']['subject'] : 'Your PDF';
						$heading            = $email_values['af_pending']['heading'] ? $email_values['af_pending']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_pending']['message'] ? $email_values['af_pending']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_in_process' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_in_process']['enable'] ) && ( 'yes' === $email_values['af_in_process']['enable'] ) && ( ! empty( $email_values['af_in_process']['subject'] ) ) ) {

						$subject            = $email_values['af_in_process']['subject'] ? $email_values['af_in_process']['subject'] : 'Your PDF';
						$heading            = $email_values['af_in_process']['heading'] ? $email_values['af_in_process']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_in_process']['message'] ? $email_values['af_in_process']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_accepted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_accepted']['enable'] ) && ( 'yes' === $email_values['af_accepted']['enable'] ) && ( ! empty( $email_values['af_accepted']['subject'] ) ) ) {

						$subject            = $email_values['af_accepted']['subject'] ? $email_values['af_accepted']['subject'] : 'Your PDF';
						$heading            = $email_values['af_accepted']['heading'] ? $email_values['af_accepted']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_accepted']['message'] ? $email_values['af_accepted']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_converted_to_cart' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_converted_to_cart']['enable'] ) && ( 'yes' === $email_values['af_converted_to_cart']['enable'] ) && ( ! empty( $email_values['af_converted_to_cart']['subject'] ) ) ) {

						$subject            = $email_values['af_converted_to_cart']['subject'] ? $email_values['af_converted_to_cart']['subject'] : 'Your PDF';
						$heading            = $email_values['af_converted_to_cart']['heading'] ? $email_values['af_converted_to_cart']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_converted_to_cart']['message'] ? $email_values['af_converted_to_cart']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}

					if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

						$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_converted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_converted']['enable'] ) && ( 'yes' === $email_values['af_converted']['enable'] ) && ( ! empty( $email_values['af_converted']['subject'] ) ) ) {

						$subject            = $email_values['af_converted']['subject'] ? $email_values['af_converted']['subject'] : 'Your PDF';
						$heading            = $email_values['af_converted']['heading'] ? $email_values['af_converted']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_converted']['message'] ? $email_values['af_converted']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}

					if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

						$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_declined' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_declined']['enable'] ) && ( 'yes' === $email_values['af_declined']['enable'] ) && ( ! empty( $email_values['af_declined']['subject'] ) ) ) {

						$subject            = $email_values['af_declined']['subject'] ? $email_values['af_declined']['subject'] : 'Your PDF';
						$heading            = $email_values['af_declined']['heading'] ? $email_values['af_declined']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_declined']['message'] ? $email_values['af_declined']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_cancelled' === $quote_status ) && ( false == $admin_add_new_qoute ) ) {

					if ( isset( $email_values['af_cancelled']['enable'] ) && ( 'yes' === $email_values['af_cancelled']['enable'] ) && ( ! empty( $email_values['af_cancelled']['subject'] ) ) ) {
						$subject            = $email_values['af_cancelled']['subject'] ? $email_values['af_cancelled']['subject'] : 'Your PDF';
						$heading            = $email_values['af_cancelled']['heading'] ? $email_values['af_cancelled']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_cancelled']['message'] ? $email_values['af_cancelled']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				}

				if ( true == $is_ajax_applied ) {

					$file_to_save_via_url = AFRFQ_URL . 'includes/pdf/pdf-files/Quotes.pdf';
					return $file_to_save_via_url;

				} else {

					$dompdf->stream('Quote.pdf');
					exit( 0 );
				}

			break;
		default:
			ob_start();
			?>
			<!DOCTYPE html>
			<html>
				<head><meta charset="UTF-8"></head>
				<style>
					dt{
						font-weight: bold;
						font-size: 10px !important;
					}
					dd{
						margin: 0px;
						font-size: 10px !important;
					}
					dt p,
					dd p{
						margin:4px 0px;
					}
					
				</style>
				
				<body>	
					<div id="addify_quote_items_container">
						<?php
						foreach ( $qoute_id_arr as $qoute_id ) {

							$quote_contents = get_post_meta( $qoute_id, 'quote_contents', true );

							$afrfq_get_qoute_by_id = get_post( $qoute_id );

							$quote_status       = get_post_meta( $qoute_id, 'quote_status', true );
							$afrfq_quote_totals = $afrfq_quote_details->get_calculated_totals( $quote_contents, $qoute_id );

							$status                         = str_replace('af_', '', $quote_status); // Remove af_ prefix
							$status = str_replace('_', ' ', $status); // Replace underscores with spaces
							$afrfq_get_qoute_status_for_pdf =  esc_attr( ucwords($status) );

							$shipping_cost = get_post_meta( $qoute_id, 'afrfq_shipping_cost', true );

							// enable price display
							$allowed_user_roles = get_option('afrfq_enable_for_specific_user_role');

							$user_id        = get_post_meta( $qoute_id, '_customer_user', true );
							$user           = ! empty( $user_id ) ? get_user_by( 'id', intval( $user_id ) ) : null;
							$user_role      = is_object( $user ) ? $user->roles : array( 'guest' );

							$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
							$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
							$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) && ( empty($allowed_user_roles) || !empty(array_intersect($user_role, $allowed_user_roles)) ) ? true : false;
					

							?>
							<!-- Company Information + Customer Information -->
							<div class="afrfq_info_div" style="font-family: sans-serif!important;">
								<div class="af_rfq_company_information" style="
									<?php
									if ('yes' == $afrfq_enable_border) {
										?>
										padding-bottom: 13px; border-bottom:2px solid <?php echo esc_attr( $afrfq_border_color ); ?>; <?php } ?>">
										
										<div class="Qoute" style="font-size:22px; display:inline-block;width:70%; line-height:32px; vertical-align:middle;" >
											<?php 
											if (!empty(get_option( 'afrfq_company_name_text' ))) {
												$afrfq_company_name = get_option( 'afrfq_company_name_text' ); 
												?>
												<h1 style="font-size:19px; margin-top:0; margin-bottom: 0;"><?php echo esc_attr( $afrfq_company_name ); ?></h1>
											<?php } elseif (!empty($af_rfq_get_site_title)) { ?>
												<h1 style="font-size:19px; margin-top:0; margin-bottom: 0;"><?php echo esc_attr($af_rfq_get_site_title); ?></h1>
											<?php } ?>
										</div>
										<div class="afrfq_company_logo_preview" style="text-align:right; display:inline-block;width:25%;vertical-align:middle;">
											<?php if ( get_option( 'afrfq_company_logo' ) ) : ?>
												<img src="<?php echo esc_url( get_option( 'afrfq_company_logo' ) ); ?>" alt="Company Logo" style="width: 80px;" />
											<?php endif; ?>
										</div>
								</div>   
										
								<div class="afrfq_company_info_sub_details" style="
									<?php
									if ('yes' == $afrfq_enable_border) {
										?>
										padding-top:10px; <?php } ?>">

									<?php if (!empty(get_option( 'afrfq_company_address' ))) { ?>
										<p style="font-size:12px; margin-bottom: 0;" ><strong style="margin-right: 12px;font-size:12px;"><?php esc_html_e( 'Address:', 'addify_b2b' ); ?></strong><?php echo esc_attr( get_option( 'afrfq_company_address' ) ); ?></p>
									<?php
									} elseif (!empty($af_rfq_get_site_address_1)) {
										?>
										<p style="font-size:12px; margin-bottom: 0;" ><strong style="margin-right: 12px;font-size:12px;"><?php esc_html_e( 'Address:', 'addify_b2b' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_1 ); ?></p>
									<?php
									} elseif (( !empty($af_rfq_get_site_address_2) )&&( empty($af_rfq_get_site_address_1) )) {
										?>
										<p style="font-size:12px; margin-bottom: 0;" ><strong style="margin-right: 12px;font-size:12px;"><?php esc_html_e( 'Address:', 'addify_b2b' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_2 ); ?></p>
									<?php } ?>

									<p style="font-size:12px; margin-bottom: 0;"><strong style="font-size:12px;"><?php esc_html_e( 'Email:', 'addify_b2b' ); ?></strong>
										<?php
										$af_rfq_email_array_pdf = explode( ',', $admin_email );
										$iteration              = 1; // Initialize an iteration counter.

										foreach ( $af_rfq_email_array_pdf as $email ) {
											if ( $iteration > 1 ) {
												// Apply margin-left to the email address starting from the second iteration.
												echo '<p style=" font-size:12px; margin-left: 43px;">' . esc_attr( $email ) . '</p>';
											} else {
												echo esc_attr( $email ) . '<br>'; // No margin-left for the first iteration.
											}
											++$iteration; // Increment the iteration counter.
										}
										?>
									</p>
								<div>
										
								<div class="" style="
									<?php
									if ('yes' == $afrfq_enable_border) {
										?>
									padding-bottom:10px; border-bottom:2px solid <?php echo esc_attr( $afrfq_border_color ); ?>; <?php } ?>">
										
									<div class="afrfq_client_info_details" style="display:inline-block; width:63%; vertical-align:top;">
										<h1 class="cust_info_text" style="font-size:19px; line-height: 31px;" ><?php esc_html_e( 'Customer Information', 'addify_b2b' ); ?></h1>
										<?php
										$afrfq_increment_j = 0;
										foreach ( $quote_fields as $key => $field ) {
											$field_id          = $field->ID;
											$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
											$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );
											$afrfq_field_value = get_post_meta( $field_id, 'afrfq_field_value', true );
											$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
											$field_data        = get_post_meta( $qoute_id, $afrfq_field_name, true );

											if ( empty( $field_data ) && ! empty( $afrfq_field_value ) ) {
												$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
											}

											if ( ( ! empty( $field_data ) ) && ( 'terms_cond' != $afrfq_field_type ) ) {
												++$afrfq_increment_j;

												if ( is_array( $field_data ) ) {

													$field_data = implode( ',', $field_data );
												}

												if ( empty( $afrfq_field_label ) ) {
													if ( 'user_login' == $afrfq_field_value ) {
														$afrfq_field_value = 'Username';
														?>
														<p  style="font-size:12px;" ><strong style="margin-right:8px; font-size:12px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
														<?php
													} else {
														$afrfq_field_value = ucwords( str_replace( '_', ' ', $afrfq_field_value ) );
														?>
														<p  style="font-size:12px;" ><strong style="margin-right:8px; font-size:12px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
														<?php
													}
												} else {
													?>
													<p  style="font-size:12px; line-height: 13px;" ><strong style="margin-right:8px;"><?php echo esc_attr( $afrfq_field_label . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  								
													<?php
												}
											}
										}
										if ( 0 == $afrfq_increment_j ) {

											?>
											<style>
												.cust_info_text{
													display:none;
												}
											</style>
											<?php
										}
										?>
									</div>

									<div class="afrfq-quote-detail" style="display:inline-block; width:35%; vertical-align:top;">
										<h1 style="font-size:18px; line-height: 31px;"><?php esc_html_e('Quote', 'addify_b2b'); ?></h1>
										<p style="font-size:12px; line-height: 13px;"><strong style="display: inline-block; vertical-align:top; width: 93px;"><?php esc_html_e( 'Quote ID:', 'addify_b2b' ); ?></strong> <span style=""><?php echo esc_attr( $qoute_id ); ?></span></p>
										<p style="font-size:12px; line-height: 13px;"><strong style="display: inline-block; vertical-align:top; width: 93px;"><?php esc_html_e( 'Quote Status:', 'addify_b2b' ); ?> </strong> <span><?php echo esc_attr( $afrfq_get_qoute_status_for_pdf ); ?></span></p>
										<p style="font-size:12px; line-height: 13px;"><strong style="display: inline-block; vertical-align:top; width: 93px;"><?php esc_html_e( 'Quote Date:', 'addify_b2b' ); ?></strong> <span><?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $afrfq_get_qoute_by_id->post_date ) ) ); ?></span></p>
									</div>

								</div>

							</div>
							
							<!-- Quote items -->
							<table cellpadding="0" cellspacing="0" style="
								<?php
								if ('yes' == $afrfq_enable_border) {
									?>
								margin-top:10px; <?php } ?> margin-bottom:14px; border-collapse: collapse; width:100%; font-family: sans-serif!important;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
								
								<thead style="">                    
									<tr style="border-bottom:1px solid #d3d3d3a3;">
										<th style="text-align:left; padding: 15px 8px 15px 0; font-family:sans-serif;  font-size: 12px;" class="thumb sortable" data-sort="string-ins"><?php esc_html_e( 'Image', 'addify_b2b' ); ?></th>
										<th style="padding:15px 8px; font-size: 12px; font-family:sans-serif; text-align: left; width:35%;" class="item sortable" data-sort="string-ins"><?php esc_html_e( 'Product', 'addify_b2b' ); ?></th>
										<?php if ( $price_display ) { ?>
										<th style="padding:15px 8px; font-size: 12px; font-family:sans-serif; width:70px;" class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Price', 'addify_b2b' ); ?></th>
										<?php } ?>
										<?php if ( $of_price_display ) { ?>
										<th style="padding:15px 8px; font-family:sans-serif;  font-size: 12px; width:90px;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered price', 'addify_b2b' ); ?></th>
										<?php } ?>
										<th style="padding:15px 8px; font-family:sans-serif;  font-size: 12px;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Quantity', 'addify_b2b' ); ?></th>
										<?php if ( $price_display ) { ?>
										<th style="padding:15px 8px; font-family:sans-serif; font-size: 12px;" width="90px" class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Subtotal', 'addify_b2b' ); ?></th>
										<?php } ?>
										<?php if ( $of_price_display ) { ?>
										<th style="padding:15px 8px; font-family:sans-serif;  font-size: 12px; width:110px;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered Subtotal', 'addify_b2b' ); ?></th>
										<?php } ?>
									</tr>
								</thead>

								<tbody>
								
									<?php
									$offered_price_subtotal = 0;

									foreach ( (array) $quote_contents as $item_id => $item ) {
										if ( isset( $item['data'] ) ) {
											$product = $item['data'];
										} else {
											continue;
										}

										if ( ! is_object( $product ) ) {
											continue;
										}

										// incase product type is custom and now the plugin which registers that product type is not active then it simply empties the quote
										// this case is mainly added to tackle composite product in quote when configurable product plugin is not active
										if (is_object($product) && $product instanceof __PHP_Incomplete_Class) {
											continue;
										}   
										
										$price                   = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
										$price                   = empty( $item['role_base_price'] ) ? $price : $item['role_base_price'];
										$price                   = empty( $item['price_calculator_price'] ) ? $price : $item['price_calculator_price'];
										$price                   = isset($item['composite_product_price']) ? $item['composite_product_price'] : $price;
									
										$qty_display             = $item['quantity'];
										$offered_price           = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;
										$product_link            = $product ? admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) : '';
										$thumbnail               = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
										$offered_price_subtotal += floatval( $offered_price ) * intval( $qty_display );
										$image                   = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
										$image_url               = isset( $image[0] ) ? $image[0] : '';

										$component_name = isset($item['component_name']) ? $item['component_name'] . '<br><br>' : '';

										?>
										<tr style="border-bottom:1px solid #d3d3d3a3;" class="item" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
											<td class="thumb" style="text-align:left; padding: 15px 8px 15px 0;">
												<?php if ( ! empty( $image_url ) ) { ?>
													<img width="40px; "  src="<?php echo esc_url( $image_url ); ?>" alt="Product Image"/>
												<?php } elseif ( ( empty( $image_url ) ) && ( extension_loaded( 'gd' ) ) ) { ?>
													<img width="40px; "  src="<?php echo esc_url( $afrfq_default_image ); ?>" alt="Product Image"/>				
												<?php } ?>
											</td>
											<td class="woocommerce-table__product-name product-name" style=" padding:15px 8px!important; text-align:left; font-size: 12px; font-family:sans-serif; color:#000; width:35%; overflow-wrap: anywhere; word-break: break-word;">
												<?php
												$product_permalink = get_edit_post_link( $product->get_id() );
												echo wp_kses_post($component_name);
												echo wp_kses_post( apply_filters( 'addify_rfq_order_item_name', $product_permalink ? sprintf( '<a href="%s" style="color:#000; text-decoration:none;">%s</a>', $product_permalink, $product->get_name() ) : $product->get_name(), $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												if (!empty($product->get_sku())) {
													echo wp_kses_post( '<div class="wc-quote-item-sku" style="font-size:9px; font-family:sans-serif; margin-top:10px;"><strong style="font-size:9px; font-family:sans-serif;">' . esc_html__( 'SKU:', 'addify_b2b' ) . '</strong> ' . esc_attr( $product->get_sku() ) . '</div>' );
												}
												echo '<span style="font-size:11px; font-family:DejaVu Sans;">' . wp_kses_post( wc_get_formatted_cart_item_data( $item ) ) . '</span>';
												?>
											</td>
											
											<?php if ( $price_display ) { ?>
												<td class="woocommerce-table__product-total product-total" style="font-family: DejaVu Sans;padding: 15px 8px; text-align:center; color:#000; width: 20%; font-size: 12px;">
													<?php echo wp_kses_post( wc_price($price) ); ?>
												</td>
											<?php } ?>

											<?php if ( $of_price_display && ( ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) ) ) ) { ?>
												<td style="font-family:DejaVu Sans;padding: 15px 8px; text-align:center; font-size: 12px; color:#000;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
												<?php echo wp_kses_post( wc_price($offered_price) ); ?>
											</td>
											<?php
											} else if ($of_price_display) {
												?>
												<td></td>
											<?php } ?>

											<td style="padding: 15px 8px; text-align:center; font-size: 12px; color:#000; font-family:sans-serif;">
												<?php echo esc_attr( $item['quantity'] ); ?>
											</td>

											<?php if ( $price_display && ( ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) ) ) ) { ?>
												<td class="woocommerce-table__product-total product-total" style="font-family:DejaVu Sans;padding: 15px 8px; text-align:center; color:#000; font-size: 12px; width: 15%;">
													<?php echo wp_kses_post( wc_price($price * $qty_display) ); ?>
												</td>
												<?php
											} else if ($price_display) {
												?>
												<td></td>
											<?php } ?>

											<?php if ( $of_price_display && ( ( !isset($item['composite_child_products']) || empty($item['composite_child_products']) ) ) ) { ?>
												<td style="font-family:DejaVu Sans;padding: 15px 8px; text-align:center; font-size: 12px; color:#000;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_b2b' ); ?>">
														<?php echo wp_kses_post( wc_price($offered_price * $qty_display) ); ?>
												</td>
											<?php
											} else if ($of_price_display) {
												?>
												<td></td>
											<?php } ?> 
										</tr>
										<?php	
									}
									?>

								</tbody>

							</table>
									
							<!-- Quote Totals -->
							<table cellpadding="0" cellspacing="0" style=" border-collapse: collapse; width:40%; font-family: sans-serif!important; margin:auto 0 auto auto	;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
								<tbody>
									<?php
									if ( isset( $afrfq_quote_totals['_subtotal'] ) && ( $price_display ) ) {
										?>
										<tr style="font-family: sans-serif;">
											<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
												<td></td>
												<td></td>
											<?php } ?>
											<td></td>
											<td></td>
											<td></td>
											<td style="padding: 8px 20px; color:#000; font-size: 12px; text-align:left; font-weight:bold;"  ><?php esc_html_e( 'Subtotal(Standard)', 'addify_b2b' ); ?></td>
											<td style="font-family:DejaVu Sans;padding: 8px; color:#000; font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price($afrfq_quote_totals['_subtotal']) ); ?></td> 
										</tr>
										<?php
									}

									if ( isset( $afrfq_quote_totals['_offered_total'] ) && ( $of_price_display ) ) {
										?>
										<tr>
											<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
												<td ></td>
												<td ></td>
											<?php } ?>
											<td ></td>
											<td></td>
											<td></td>
											<td style="padding: 8px 20px; color:#000; font-size: 12px; text-align:left;  font-weight:bold;"><?php esc_html_e( 'Offered Subtotal', 'addify_b2b' ); ?></td>
											<td style="font-family:DejaVu Sans;padding: 8px; color:#000;  font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price($afrfq_quote_totals['_offered_total']) ); ?></td> 
										</tr>
										<?php
									}

									if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_tax_total'] ) && ( ( $price_display ) || ( $of_price_display ) ) && $tax_display ) {
										?>
										<tr style="font-family:sans-serif;">
											<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
												<td ></td>
												<td ></td>
											<?php } ?>
											<td ></td>
											<td></td>
											<td></td>
											<td style="padding: 8px 20px; color:#000; font-size: 12px; text-align:left; font-weight:bold;" ><?php esc_html_e( 'VAT(Standard)', 'addify_b2b' ); ?></td>
											<td style="font-family:DejaVu Sans;padding: 8px; color:#000; font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price($afrfq_quote_totals['_tax_total']) ); ?></td> 
										</tr>
										<?php
									}

									if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_offered_tax_total'] ) && ( $price_display ) && $tax_display ) {
										?>
										<tr style="font-family:sans-serif;">
											<?php if (  $of_price_display ) { ?>
												<td ></td>
												<td ></td>
											<?php } ?>
											<td ></td>
											<td></td>
											<td></td>
											<td style="padding: 8px 20px; color:#000; font-size: 12px; text-align:left; font-weight:bold;" ><?php esc_html_e( 'Offered VAT', 'addify_b2b' ); ?></td>
											<td style="font-family:DejaVu Sans;padding: 8px; color:#000; font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price($afrfq_quote_totals['_offered_tax_total']) ); ?></td> 
										</tr>
										<?php
									}

									if ( $shipping_cost && ( ( $price_display ) || ( $of_price_display ) ) ) {
										?>
										<tr style="font-family:sans-serif;">
											<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
												<td ></td>
												<td ></td>
											<?php } ?>
											<td ></td>
											<td></td>
											<td></td>
											<td style="padding: 8px 20px; color:#000; font-size: 12px; text-align:left; font-weight:bold;" ><?php esc_html_e( 'Shipping Cost', 'addify_b2b' ); ?></td>
											<td style="font-family:DejaVu Sans;padding: 8px; color:#000; font-size: 12px; text-align:right;" ><?php echo wp_kses_post( wc_price($shipping_cost) ); ?></td> 
										</tr>
										<?php
									}


									if ( isset( $afrfq_quote_totals['_total'] ) && ( $price_display ) ) {
										?>
										<tr style="font-family:sans-serif;">
											<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
												<td ></td>
												<td ></td>
											<?php } ?>
											<td ></td>
											<td ></td>
											<td ></td>
											<td style="padding: 8px 20px; font-size: 12px; text-align:left;  font-weight:bold;  border: none!important; color:#000;"><?php esc_html_e( 'Total(Standard)', 'addify_b2b' ); ?></td>
											<td style="font-family:DejaVu Sans ;padding: 8px; font-size: 12px;  text-align:right; border: none!important; color:#000;"><?php echo wp_kses_post( wc_price($afrfq_quote_totals['_total']) ); ?></td> 
										</tr>
										<?php
									} 

									if ( isset( $afrfq_quote_totals['_offered_total_after_tax'] ) && ( $of_price_display ) ) {
										?>
										<tr style="font-family:sans-serif;">
											<?php if ( ( $price_display ) && ( $of_price_display ) ) { ?>
												<td ></td>
												<td ></td>
											<?php } ?>
											<td ></td>
											<td ></td>
											<td ></td>
											<td style="padding: 8px 20px; font-size: 12px; text-align:left;  font-weight:bold;  border: none!important; color:#000;"><?php esc_html_e( 'Offered Total', 'addify_b2b' ); ?></td>
											<td style="font-family:DejaVu Sans;padding: 8px; font-size: 12px;  text-align:right; border: none!important; color:#000;"><?php echo wp_kses_post( wc_price($afrfq_quote_totals['_offered_total_after_tax']) ); ?></td> 
										</tr>
										<?php 
									} 
									?>
								</tbody>
							</table>

							<!-- Terms and conditions -->
							<?php if ( 'yes' == get_option( 'afrfq_enable_term_and_condition' ) ) { ?>
								<div class="afrfq_client_info_details" style="font-family:sans-serif; 
									<?php
									if ('yes' == $afrfq_enable_border) {
										?>
										margin-top:12px; border-top:2px solid <?php echo esc_attr( $afrfq_border_color ); ?>; <?php } ?>">
									<h1 style="font-family:sans-serif; margin-bottom:10px; font-size:18px; line-height:28px;" ><?php esc_html_e( 'Terms & Conditions', 'addify_b2b' ); ?></h1>
									<ul style="font-family:sans-serif; margin:0px; padding:0 15px 15px; font-size: 12px; line-height:23px" >
										<li><?php echo esc_attr( $af_rfq_privicy_and_term_conditions ); ?></li>
									</ul>
								</div>
							<?php } ?>

						<?php } ?>

					</div>
				</body>
			</html>
			
			<?php

			$html = ob_get_clean();
			$dompdf->loadHtml( $html );
			$dompdf->setPaper( 'A4', 'portrait' );
			$dompdf->render();
			$pdf_content   = $dompdf->output();
			$pdf_file_name = 'Qoute_' . current( $qoute_id_arr ) . '.pdf';

			$file_to_save = AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf';
			file_put_contents( $file_to_save, $pdf_content );

			if ( ( true == $afrfq_is_multiple_quotes_downloaded ) && ( ( 'yes' == $afrfq_admin_email_pdf ) ) ) {
				$to          = $admin_email;
				$subject     = 'Your PDF';
				$message     = 'Here is the PDF you requested.';
				$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
				$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
				wp_mail( $to, $subject, $message, $headers, $attachments );

			} elseif ( ( true == $is_ajax_applied ) && ( true == $is_qoute_status_update ) ) {
				$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
				return $attachments;

			} elseif ( ( true == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) {
				if ( isset( $email_values['af_admin']['enable'] ) && ( 'yes' === $email_values['af_admin']['enable'] ) && ( ! empty( $email_values['af_admin']['subject'] ) ) ) {
					$subject            = $email_values['af_admin']['subject'] ? $email_values['af_admin']['subject'] : 'Your PDF';
					$heading            = $email_values['af_admin']['heading'] ? $email_values['af_admin']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_admin']['message'] ? $email_values['af_admin']['message'] : '';
					$message            = $heading . $additional_message;
					if ( ( ! empty( $admin_email ) ) && ( 'yes' == $afrfq_admin_email_pdf ) && ( true == $af_rfq_is_email_send_to_admin ) ) {
						$to          = $admin_email;
						$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}
			} elseif ( ( 'af_pending' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {
				if ( isset( $email_values['af_pending']['enable'] ) && ( 'yes' === $email_values['af_pending']['enable'] ) && ( ! empty( $email_values['af_pending']['subject'] ) ) ) {
					$subject            = $email_values['af_pending']['subject'] ? $email_values['af_pending']['subject'] : 'Your PDF';
					$heading            = $email_values['af_pending']['heading'] ? $email_values['af_pending']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_pending']['message'] ? $email_values['af_pending']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $af_rfq_user_email ) && ( 'yes' == $afrfq_user_email_pdf ) && ( true == $af_rfq_is_email_send_to_user ) ) && ( 'Guest' != $user_name ) ) {
						$to = $af_rfq_user_email;
						$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );
					}

					if ( ( ! empty( $admin_email ) ) && ( 'yes' == $afrfq_admin_email_pdf ) && ( true == $af_rfq_is_email_send_to_admin ) ) {
						$to          = $admin_email;
						$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}
			} elseif ( ( 'af_in_process' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

				if ( isset( $email_values['af_in_process']['enable'] ) && ( 'yes' === $email_values['af_in_process']['enable'] ) && ( ! empty( $email_values['af_in_process']['subject'] ) ) ) {

					$subject            = $email_values['af_in_process']['subject'] ? $email_values['af_in_process']['subject'] : 'Your PDF';
					$heading            = $email_values['af_in_process']['heading'] ? $email_values['af_in_process']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_in_process']['message'] ? $email_values['af_in_process']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $af_rfq_user_email )
					&& ( 'yes' == $afrfq_user_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_user ) )
					&& ( 'Guest' != $user_name ) ) {

						$to = $af_rfq_user_email;

						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}

					if ( ( ! empty( $admin_email ) )
					&& ( 'yes' == $afrfq_admin_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_admin ) ) {

						$to          = $admin_email;
						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}
			} elseif ( ( 'af_accepted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

				if ( isset( $email_values['af_accepted']['enable'] ) && ( 'yes' === $email_values['af_accepted']['enable'] ) && ( ! empty( $email_values['af_accepted']['subject'] ) ) ) {

					$subject            = $email_values['af_accepted']['subject'] ? $email_values['af_accepted']['subject'] : 'Your PDF';
					$heading            = $email_values['af_accepted']['heading'] ? $email_values['af_accepted']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_accepted']['message'] ? $email_values['af_accepted']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $af_rfq_user_email )
					&& ( 'yes' == $afrfq_user_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_user ) )
					&& ( 'Guest' != $user_name ) ) {

						$to = $af_rfq_user_email;

						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}

					if ( ( ! empty( $admin_email ) )
					&& ( 'yes' == $afrfq_admin_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_admin ) ) {

						$to          = $admin_email;
						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}
			} elseif ( ( 'af_converted_to_cart' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

				if ( isset( $email_values['af_converted_to_cart']['enable'] ) && ( 'yes' === $email_values['af_converted_to_cart']['enable'] ) && ( ! empty( $email_values['af_converted_to_cart']['subject'] ) ) ) {

					$subject            = $email_values['af_converted_to_cart']['subject'] ? $email_values['af_converted_to_cart']['subject'] : 'Your PDF';
					$heading            = $email_values['af_converted_to_cart']['heading'] ? $email_values['af_converted_to_cart']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_converted_to_cart']['message'] ? $email_values['af_converted_to_cart']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $af_rfq_user_email )
					&& ( 'yes' == $afrfq_user_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_user ) )
					&& ( 'Guest' != $user_name ) ) {

						$to = $af_rfq_user_email;

						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}

				if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

					$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
					$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $admin_email ) )
					&& ( 'yes' == $afrfq_admin_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_admin ) ) {

						$to          = $admin_email;
						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}
			} elseif ( ( 'af_converted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

				if ( isset( $email_values['af_converted']['enable'] ) && ( 'yes' === $email_values['af_converted']['enable'] ) && ( ! empty( $email_values['af_converted']['subject'] ) ) ) {

					$subject            = $email_values['af_converted']['subject'] ? $email_values['af_converted']['subject'] : 'Your PDF';
					$heading            = $email_values['af_converted']['heading'] ? $email_values['af_converted']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_converted']['message'] ? $email_values['af_converted']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $af_rfq_user_email )
					&& ( 'yes' == $afrfq_user_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_user ) )
					&& ( 'Guest' != $user_name ) ) {

						$to = $af_rfq_user_email;

						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}

				if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

					$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
					$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $admin_email ) )
					&& ( 'yes' == $afrfq_admin_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_admin ) ) {

						$to          = $admin_email;
						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}
			} elseif ( ( 'af_declined' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {
				if ( isset( $email_values['af_declined']['enable'] ) && ( 'yes' === $email_values['af_declined']['enable'] ) && ( ! empty( $email_values['af_declined']['subject'] ) ) ) {

					$subject            = $email_values['af_declined']['subject'] ? $email_values['af_declined']['subject'] : 'Your PDF';
					$heading            = $email_values['af_declined']['heading'] ? $email_values['af_declined']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_declined']['message'] ? $email_values['af_declined']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $af_rfq_user_email )
					&& ( 'yes' == $afrfq_user_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_user ) )
					&& ( 'Guest' != $user_name ) ) {

						$to = $af_rfq_user_email;

						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}

					if ( ( ! empty( $admin_email ) )
					&& ( 'yes' == $afrfq_admin_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_admin ) ) {

						$to          = $admin_email;
						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}
			} elseif ( ( 'af_cancelled' === $quote_status ) && ( false == $admin_add_new_qoute ) ) {
				if ( isset( $email_values['af_cancelled']['enable'] ) && ( 'yes' === $email_values['af_cancelled']['enable'] ) && ( ! empty( $email_values['af_cancelled']['subject'] ) ) ) {
					$subject            = $email_values['af_cancelled']['subject'] ? $email_values['af_cancelled']['subject'] : 'Your PDF';
					$heading            = $email_values['af_cancelled']['heading'] ? $email_values['af_cancelled']['heading'] : 'Here is the PDF you requested.';
					$additional_message = $email_values['af_cancelled']['message'] ? $email_values['af_cancelled']['message'] : '';
					$message            = $heading . $additional_message;

					if ( ( ! empty( $af_rfq_user_email )
					&& ( 'yes' == $afrfq_user_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_user ) )
					&& ( 'Guest' != $user_name ) ) {

						$to = $af_rfq_user_email;

						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}

					if ( ( ! empty( $admin_email ) )
					&& ( 'yes' == $afrfq_admin_email_pdf )
					&& ( true == $af_rfq_is_email_send_to_admin ) ) {

						$to          = $admin_email;
						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					}
				}
			}

			if ( true == $is_ajax_applied ) {

				$file_to_save_via_url = AFRFQ_URL . 'includes/pdf/pdf-files/Quotes.pdf';
				return $file_to_save_via_url;

			} else {
				$dompdf->stream('Quote.pdf');
				exit( 0 );
			}

			break;
	}
}


if (!function_exists('afrfq_add_quote_note')) {
	function afrfq_add_quote_note( $quote_id, $quote_message, $is_customer_note = false ) {
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');

		if ('' == $quote_message) {
			return false;
		}
		
		$note_array[] = array(
			'message'           => $quote_message,
			'is_customer_note'  => $is_customer_note,
			'datetime'          => current_time('mysql'), 
			'date'              => date_i18n($date_format, current_time('timestamp')),
			'time'              => date_i18n($time_format, current_time('timestamp')),
		);

		if ('' != $quote_id) {
			$quote_notes = get_post_meta($quote_id, 'afrfq_quote_notes', true);

			if (empty($quote_notes)) {
				update_post_meta($quote_id, 'afrfq_quote_notes', $note_array);
				$message_added = true;
			} else {
				update_post_meta($quote_id, 'afrfq_quote_notes', array_merge($quote_notes, $note_array));
				$message_added = true;
			}
		} else {
			$message_added = false;
		}

		return $message_added;
	}
}

if (!function_exists('afrfq_is_price_calculator_product_managed')) {
	function afrfq_is_price_calculator_product_managed( $product_id ) {
		$product = wc_get_product($product_id);

		$is_stock_managed = get_post_meta($product_id, 'enable_prc_stock_managment', true);
		if ('yes' == $is_stock_managed) {
			return true;
		}
		if ($product->is_type( 'variation' )) {
			$is_stock_managed = get_post_meta($product->get_parent_id(), 'enable_prc_stock_managment', true);
			if ('yes' == $is_stock_managed) {
				return 'parent';
			}
		}
		return false;
	}
}






