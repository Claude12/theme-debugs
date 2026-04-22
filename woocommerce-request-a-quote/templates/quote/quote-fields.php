<?php
/**
 * Addify Add to Quote Fields
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/quote-fields.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

$user_id = get_current_user_id();

$quote_fields_obj = new AF_R_F_Q_Quote_Fields();
$quote_fields     = (array) $quote_fields_obj->quote_fields;
$cache_data       = wc()->session->get( 'quote_fields_data' );
?>

<div class="quote-fields">

	<?php
	foreach ( $quote_fields as $key => $field ) :

		$field_id = $field->ID;

		$afrfq_field_name        = get_post_meta( $field_id, 'afrfq_field_name', true );
		$afrfq_field_type        = get_post_meta( $field_id, 'afrfq_field_type', true );
		$afrfq_field_label       = get_post_meta( $field_id, 'afrfq_field_label', true );
		$afrfq_field_value       = get_post_meta( $field_id, 'afrfq_field_value', true );
		$afrfq_field_title       = get_post_meta( $field_id, 'afrfq_field_title', true );
		$afrfq_field_placeholder = get_post_meta( $field_id, 'afrfq_field_placeholder', true );
		$afrfq_field_options     = get_post_meta( $field_id, 'afrfq_field_options', true );
		$afrfq_file_types        = get_post_meta( $field_id, 'afrfq_file_types', true );
		$afrfq_file_size         = get_post_meta( $field_id, 'afrfq_file_size', true );
		$afrfq_field_enable      = get_post_meta( $field_id, 'afrfq_field_enable', true );
		$afrfq_field_terms       = get_post_meta( $field_id, 'afrfq_field_terms', true );
		$afrfq_field_width       = get_post_meta( $field_id, 'afrfq_field_width', true );

		if ( isset( $cache_data[ $afrfq_field_name ] ) ) {
			$field_data = $cache_data[ $afrfq_field_name ];
		} else {
			$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
		}

		if ( empty( $afrfq_field_name ) ) {
			continue;
		}
	
		if (in_array($afrfq_field_type, array( 'select', 'multiselect', 'radio', 'checkbox' )) && empty($afrfq_field_options)) {
			continue;
		}

		$required = 'yes' === get_post_meta( $field_id, 'afrfq_field_required', true ) ? 'required=required' : '';

		if ( 'terms_cond' == $afrfq_field_type ) {
			?>
			<p class="addify-option-field <?php echo !empty($afrfq_field_width) ? 'adf_' . esc_html( $afrfq_field_width ): 'adf_full_width'; ?> adf-term-conditon">
				<input type="checkbox" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="yes" <?php echo esc_attr( $required ); ?> >
				<span><?php echo wp_kses_post( $afrfq_field_terms ); ?></span>
			</p>
			<?php
			continue;
		}

		?>
		<p class="addify-option-field  <?php echo !empty($afrfq_field_width) ? 'adf_' . esc_html( $afrfq_field_width ): 'adf_full_width'; ?>">
				<label><?php echo esc_html( $afrfq_field_label ); ?></label>
				<?php
				switch ( $afrfq_field_type ) {
					case 'text':
						?>
							<input type="text" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>" <?php echo esc_attr( $required ); ?> >
						<?php
						break;
					case 'time':
						?>
							<input type="time" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>" <?php echo esc_attr( $required ); ?> >
						<?php
						break;
					case 'date':
						?>
							<input type="date" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>" <?php echo esc_attr( $required ); ?> >
						<?php
						break;
					case 'datetime':
						?>
							<input type="datetime-local" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>" <?php echo esc_attr( $required ); ?> >
						<?php
						break;
					case 'email':
						?>
							<input type="email" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>" <?php echo esc_attr( $required ); ?> >
						<?php
						break;
					case 'number':
						?>
							<input type="number" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>" <?php echo esc_attr( $required ); ?> >
						<?php
						break;
					case 'file':
						$types = array();
						foreach ( (array) explode( ',', $afrfq_file_types ) as $file_type ) {
							$types[] = strtolower( $file_type );
							$types[] = strtoupper( $file_type );
						}

						$pattern = '([a-zA-Z0-9\s_\\.\-\(\):]).+(' . implode( '|', $types ) . ')$';
						?>
						<input type="file" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>" <?php echo esc_attr( $required ); ?> >
						<?php
						break;
					case 'textarea':
						?>
							<textarea placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" <?php echo esc_attr( $required ); ?> ><?php echo esc_html( $field_data ); ?></textarea>
						<?php
						break;
					case 'select':
						?>
							<select name="<?php echo esc_html( $afrfq_field_name ); ?>" <?php echo esc_attr( $required ); ?> class="adf-rqst-select" >
							<?php
							foreach ( (array) $afrfq_field_options as $option ) :
								$value = strtolower( trim( $option ) );
								?>
									<option value="<?php echo esc_html( $value ); ?>" <?php echo selected( $value, $field_data ); ?> ><?php echo esc_html( $option ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php
						break;
					case 'multiselect':
						?>
							<select placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>[]" multiple <?php echo esc_attr( $required ); ?> class="adf-multi-select">
							<?php
							foreach ( (array) $afrfq_field_options as $option ) :
								$value = strtolower( trim( $option ) );
								?>
									<option value="<?php echo esc_html( $value ); ?>" <?php echo in_array( $value, (array) $field_data, true ) ? 'selected' : ''; ?> > <?php echo esc_html( $option ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						<?php
						break;
					case 'radio':
						foreach ( (array) $afrfq_field_options as $option ) :
							$value = strtolower( trim( $option ) );
							?>
							<label class="adf-radio-btn">
								<input placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" type="radio" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $value ); ?>" <?php echo checked( $value, $field_data ); ?> <?php echo esc_attr( $required ); ?> ><?php echo esc_html( $option ); ?>
							</label>
							<?php
						endforeach;
						break;
					case 'checkbox':
						foreach ( (array) $afrfq_field_options as $option ) :
							$value = strtolower( trim( $option ) );
							?>
						<label class="adf-chekboxes">
							<input placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" type="checkbox" name="<?php echo esc_html( $afrfq_field_name ); ?>[]" value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $required ); ?> <?php echo checked( in_array( $value, (array) $field_data ), true ); ?> >
							<?php echo esc_html( $option ); ?>
						</label>
						<?php
						endforeach;
						break;
				}
				?>
			</p>

	<?php endforeach; ?>

</div>
