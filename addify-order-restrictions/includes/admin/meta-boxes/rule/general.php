<?php

defined( 'ABSPATH' ) || exit;

global $post, $wp_roles;

$afor_customers           = (array) get_post_meta( $post->ID, 'afor_customers', true );
$afor_user_roles          = (array) get_post_meta( $post->ID, 'afor_user_roles', true );
$afor_min_quantity        = get_post_meta( $post->ID, 'afor_min_quantity', true );
$afor_max_quantity        = get_post_meta( $post->ID, 'afor_max_quantity', true );
$afor_min_amount          = get_post_meta( $post->ID, 'afor_min_amount', true );
$afor_max_amount          = get_post_meta( $post->ID, 'afor_max_amount', true );
$afor_cart_amount         = get_post_meta( $post->ID, 'afor_cart_amount', true );
$afor_restriction_message = get_post_meta( $post->ID, 'afor_restriction_message', true );

$afor_cart_amount = empty( $afor_cart_amount ) ? 'subtotal' : $afor_cart_amount;
?>
<div class="afrfb-metabox-fields">
	<?php wp_nonce_field('afor_nonce_action', 'afor_nonce_field'); ?>
	<table class="addify-table-optoin">
		<tr class="addify-option-field">
			<th>
				<h3>
					<?php esc_html_e( 'Select Customer', 'addify_b2b' ); ?>
				</h3>
			</th>
			<td>
				<select class="afor_customers" multiple name="afor_customers[]" placeholder="<?php esc_html_e('Choose Customers', 'addify_b2b'); ?>" >
					<?php 
					foreach ( $afor_customers as $customer_id ) {

						if ( empty( $customer_id ) ) {
							continue;
						}
						?>
						<option value="<?php echo intval( $customer_id ); ?>" selected>
							<?php
							$user = get_user_by('id', $customer_id );
							echo esc_html( $user->display_name );
							echo '(' . esc_html( $user->user_email ) . ')';
							?>
						</option>
						<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<h3>
					<?php esc_html_e( 'Select User Role', 'addify_b2b' ); ?>
				</h3>
			</th>
			<td>
				<select class="afor_user_roles" multiple name="afor_user_roles[]"  placeholder="<?php esc_html_e('Choose Customers', 'addify_b2b'); ?>" >
					<?php 
					foreach ( $wp_roles->get_names() as $user_role => $user_role_label ) {
						?>
						<option value="<?php echo esc_attr( $user_role ); ?>" <?php echo selected ( in_array( $user_role, $afor_user_roles ), true ); ?> >
							<?php echo esc_html( $user_role_label ); ?>
						</option>
						<?php
					}
					?>
					<option value="guest" <?php echo selected ( in_array( 'guest', $afor_user_roles ), true ); ?> >
						<?php esc_html_e('Guest', 'addify_b2b'); ?>
					</option>
				</select>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<h3>
					<?php esc_html_e( 'Order Quantity', 'addify_b2b' ); ?>
				</h3>
			</th>
			<td>
				<label for=""><?php esc_html_e( 'Minimum Quantity', 'addify_b2b' ); ?></label>
				<input type="number" name="afor_min_quantity" value="<?php echo intval( $afor_min_quantity ); ?>">
				<br>
				<br>
				<label for=""><?php esc_html_e( 'Maximum Quantity', 'addify_b2b' ); ?></label>
				<input type="number" name="afor_max_quantity" value="<?php echo intval( $afor_max_quantity ); ?>">
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<h3>
					<?php esc_html_e( 'Order Amount', 'addify_b2b' ); ?>
				</h3>
			</th>
			<td>
				<label for=""><?php esc_html_e( 'Minimum Amount', 'addify_b2b' ); ?></label>
				<input type="number" name="afor_min_amount" value="<?php echo intval( $afor_min_amount ); ?>">
				<br>
				<br>
				<label for=""><?php esc_html_e( 'Maximum Amount', 'addify_b2b' ); ?></label>
				<input type="number" name="afor_max_amount" value="<?php echo intval( $afor_max_amount ); ?>">
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<h3>
					<?php esc_html_e( 'Compare Cart Amount', 'addify_b2b' ); ?>
				</h3>
			</th>
			<td>
				<input type="radio" name="afor_cart_amount" value="subtotal" <?php echo checked( 'subtotal', $afor_cart_amount ); ?> >
				<?php esc_html_e( 'Cart subtotal', 'addify_b2b' ); ?>
				<br>
				<input type="radio" name="afor_cart_amount" value="total" <?php echo checked( 'total', $afor_cart_amount ); ?>>
				<?php esc_html_e( 'Cart Total', 'addify_b2b' ); ?>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<h3>
					<?php esc_html_e( 'Restriction Message', 'addify_b2b' ); ?>
				</h3>
			</th>
			<td>
				<?php
				$content     = wpautop( wptexturize( $afor_restriction_message ) );
				$editor_name = 'afor_restriction_message';
				$editor_id   = 'afor_restriction_message';
				$settings    = array(
					// Disable autop if the current post has blocks in it.
					'wpautop'             => false,
					'media_buttons'       => false,
					'default_editor'      => '',
					'drag_drop_upload'    => false,
					'textarea_name'       => $editor_name,
					'textarea_rows'       => 5,
					'tabindex'            => '',
					'tabfocus_elements'   => ':prev,:next',
					'editor_css'          => '',
					'editor_class'        => '',
					'teeny'               => false,
					'_content_editor_dfw' => false,
					'tinymce'             => true,
					'quicktags'           => true,
				);

				wp_editor( $content, $editor_id, $settings );
				?>
				<p class="description"><?php esc_html_e('Restriction Message to show customers.', 'addify_b2b'); ?></p>
				<p class="description"><?php esc_html_e('Available placeholders are: {remaining_qunatity}, {remaining_amount}, {exceeded_quntiity}, {exceeded_amount}.', 'addify_b2b'); ?></p>
			</td>
		</tr>
	</table>
</div>
