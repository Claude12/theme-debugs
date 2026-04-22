<?php

$afb2b_role_template_heading_text = !empty(get_option('afb2b_role_template_heading_text')) ? get_option('afb2b_role_template_heading_text') : 'Select your Deal';
$afb2b_role_template_icon         = !empty(get_option('afb2b_role_template_icon')) ? get_option('afb2b_role_template_icon') : AFB2B_URL . '/images/fire.png';
$afb2b_role_pricing_design_type   = !empty(get_option('afb2b_role_pricing_design_type')) ? get_option('afb2b_role_pricing_design_type') : 'default_template';

$afb2b_role_table_header_color      = !empty(get_option('afb2b_role_table_header_color')) ? get_option('afb2b_role_table_header_color') : '#FFFFFF';
$afb2b_role_table_header_text_color = !empty(get_option('afb2b_role_table_header_text_color')) ? get_option('afb2b_role_table_header_text_color') : '#000000';
$afb2b_role_table_header_font_size  = !empty(get_option('afb2b_role_table_header_font_size')) ? get_option('afb2b_role_table_header_font_size') : '18';

$csp_range_msg = get_option('csp_range_msg');
if ('' == $csp_range_msg) {
	$csp_range_msg = '{min_qty}  -  {max_qty} quantity the price is {pro_price}/each';
}

if (!empty($afb2b_role_data_for_template_design)) {

	?>
	<div class="afb2b_role_template_div">

		<?php if ('yes' === get_option('afb2b_role_enable_template_heading') || 'yes' === get_option('afb2b_role_enable_template_icon')) : ?>
			<div class="afb2b_role_template_header">
				<?php if ('yes' === get_option('afb2b_role_enable_template_icon')) : ?>
					<img src="<?php echo esc_url($afb2b_role_template_icon); ?>" class="afb2b_role_deals_icon">
				<?php endif; ?>
				<?php if ('yes' === get_option('afb2b_role_enable_template_heading')) : ?>
					<h2><?php echo esc_attr($afb2b_role_template_heading_text); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
		$table_template_for_dynamic_price_change = '';
		$table_template                          = '';

		$table_template .= '<div class="afb2b_role_table_div"><table class="afb2b_role_table"><thead><tr>';

		//table header
		$header_html  = '';
		$header_html .= '<th style="color:' . esc_attr($afb2b_role_table_header_text_color) . '; background-color:' . esc_attr($afb2b_role_table_header_color) . '; font-size: ' . esc_attr($afb2b_role_table_header_font_size) . 'px;">' . esc_html__('Min', 'addify_b2b') . '</th>';
		$header_html .= '<th style="color:' . esc_attr($afb2b_role_table_header_text_color) . '; background-color:' . esc_attr($afb2b_role_table_header_color) . '; font-size: ' . esc_attr($afb2b_role_table_header_font_size) . 'px;">' . esc_html__('Max', 'addify_b2b') . '</th>';
		$header_html .= '<th style="color:' . esc_attr($afb2b_role_table_header_text_color) . '; background-color:' . esc_attr($afb2b_role_table_header_color) . '; font-size: ' . esc_attr($afb2b_role_table_header_font_size) . 'px;">' . esc_html__('Price', 'addify_b2b') . '</th>';

		if ('yes' === get_option('afb2b_role_enable_save_column')) {
			$header_html .= '<th class="afb2b_role_table_save_col" style="color:' . esc_attr($afb2b_role_table_header_text_color) . '; background-color:' . esc_attr($afb2b_role_table_header_color) . '; font-size: ' . esc_attr($afb2b_role_table_header_font_size) . 'px;">' . esc_html__('Save', 'addify_b2b') . '</th>';
		}

		$table_template .= $header_html;

		$table_template                          .= '</tr></thead><tbody>';
		$table_template_for_dynamic_price_change .= '<table class="dynamic_price_display"><tbody>';

		$row_html                  = '';
		$row_html_for_price_change ='';


		foreach ($afb2b_role_data_for_template_design as $value) {
			$min_qty          = $value['min_qty'];
			$max_qty          = isset($value['max_qty']) && '' !== $value['max_qty'] && '0' != $value['max_qty'] ? $value['max_qty'] : '-';
			$discounted_price = $value['discounted_price'];
			$saved_amount     = $value['saved_amount'] > 0 ? $value['saved_amount'] : 0;
			$replace_price    = $value['replace_original_price'];
			// pro price is coming from main role based front class it is price of product
			if ($pro_price<= $discounted_price) {
				$replace_price = 'yes';
			}

			$row_html .= '<tr>';
			$row_html .= '<td>' . $min_qty . '</td>';
			$row_html .= '<td>' . $max_qty . '</td>';
			$row_html .= '<td>' . wc_price($discounted_price) . '</td>';

			if ('yes' === get_option('afb2b_role_enable_save_column')) {
				$row_html .= '<td class="afb2b_role_table_save_col">' . wc_price($saved_amount) . '</td>';
			}

			$row_html .= '</tr>';

			$row_html_for_price_change .= '<tr>';
			$row_html_for_price_change .= '<td data-replace="' . $replace_price . '">' . $min_qty . '</td>';
			$row_html_for_price_change .= '<td>' . $max_qty . '</td>';
			$row_html_for_price_change .= '<td>' . wc_price($discounted_price) . '</td>';
			$row_html_for_price_change .= '<td>' . wc_price($saved_amount) . '</td>';
			$row_html_for_price_change .= '<td>' . wc_price($pro_price) . '</td>';
			$row_html_for_price_change .= '</tr>';
		}

		$table_template                          .= $row_html . '</tbody></table></div>';
		$table_template_for_dynamic_price_change .= $row_html_for_price_change . '</tbody></table>';

		if ('default_template' === $afb2b_role_pricing_design_type) {
			$template_html = '<div class="afb2b_role_default_div"><ul>';
			foreach ($afb2b_role_data_for_template_design as $value) {
				$min_qty          = $value['min_qty'];
				$max_qty          = isset($value['max_qty']) && '' !== $value['max_qty'] ? $value['max_qty'] : '-';
				$discounted_price = $value['discounted_price'];
				$ad_msg           = str_replace('{min_qty}', $min_qty, $csp_range_msg);
				$ad_msg           = str_replace('{max_qty}', $max_qty, $ad_msg);
				$ad_msg           = str_replace('{pro_price}', wc_price($discounted_price), $ad_msg);
				$template_html   .= '<li>' . $ad_msg . '</li>';
			}
			$template_html .= '</ul></div><br>';
			echo wp_kses_post($template_html);
		} elseif ('card' === $afb2b_role_pricing_design_type) {
			$card_html = '';
			foreach ($afb2b_role_data_for_template_design as $value) {
				$min_qty             = $value['min_qty'];
				$discounted_price    = $value['discounted_price'];
				$saved_amount        = $value['saved_amount'];
				$original_price      = $discounted_price + $saved_amount;
				$discount_percentage = $original_price > 0 ? round(( $saved_amount / $original_price ) * 100) : 0;
				$headingText         = "Buy $min_qty or<br> more";
				$discount_text       = $saved_amount > 0 ? '<del>' . wc_price($original_price) . '</del>' : '<span class="afb2b_role_no_discount">No Discount</span>';

				$card_html .= '
						<div class="afb2b_role_inner_small_box" data-min-qty="' . $min_qty . '">
							<div class="afb2b_role_offer_data_contianer">
								<div class="afb2b_role_card_inner_heading">' . $headingText . '</div>
								<div class="afb2b_role_card_inner_text">
									<p>' . wc_price($discounted_price) . '/each</p>
									<p>' . $discount_text . '</p>
								</div>
							</div>';

				if ('yes' === get_option('afb2b_role_enable_card_sale_tag')) {
					$card_html .= '<div class="afb2b_role_sale_tag">' . $discount_percentage . '%</div>';
				}

				$card_html .= '</div>';
			}
			echo '<div class="afb2b_role_card_div">' . wp_kses_post($card_html) . '</div>';
			
		} elseif ('list' === $afb2b_role_pricing_design_type) {
			$list_html = '';
			foreach ($afb2b_role_data_for_template_design as $value) {
				$min_qty             = $value['min_qty'];
				$max_qty             = $value['max_qty'];
				$discounted_price    = $value['discounted_price'];
				$saved_amount        = $value['saved_amount'];
				$original_price      = $discounted_price + $saved_amount;
				$discount_percentage = $saved_amount > 0 ? round(( $saved_amount / $original_price ) * 100) : 0;

				$headingText = "Buy $min_qty or more";
							

				$headingText .= $saved_amount > 0 ? " & save upto $discount_percentage%" : '';

				$discount_text = $saved_amount > 0 ? '<del>' . wc_price($original_price) . '</del>' : '<span class="afb2b_role_no_discount">No Discount</span>';
				
				$list_html .= '
						<div class="afb2b_role_list_box" data-min-qty="' . $min_qty . '">
							<div class="afb2b_role_list_inner_container">
								<div class="afb2b_role_radio_div"></div>
								<div class="heading">' . $headingText . '</div>
								<div class="afb2b_role_list_price_text">
									<p>' . wc_price($discounted_price) . '/each</p>
									<p>' . $discount_text . '</p>
								</div>
							</div>
						</div>';
			}
				echo '<div class="afb2b_role_list_div">' . wp_kses_post($list_html) . '</div>';
		} elseif ('table' === $afb2b_role_pricing_design_type) {
			echo wp_kses_post($table_template);
		}
		?>
	</div>
	<?php
	// default table that is used to handle price display

	echo wp_kses_post($table_template_for_dynamic_price_change);

}




