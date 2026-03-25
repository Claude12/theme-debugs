<?php
/**
 * Product field class.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @copyright Barn2 Media Ltd
 * @license   GPL-3.0
 */

namespace Barn2\Plugin\WC_Product_Options\Fields;

use Barn2\Plugin\WC_Product_Options\Fields\Traits\With_Quantity_Pickers;
use Barn2\Plugin\WC_Product_Options\Util\Cart as Cart_Util;

use WP_Error;

use function Barn2\Plugin\WC_Product_Options\wpo;

/**
 * Product field class.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 */
class Products extends Abstract_Field {

	use With_Quantity_Pickers;

	/**
	 * Whether the field supports multiple values (e.g checkboxes).
	 *
	 * @var bool
	 */
	protected $stores_multiple_values = true;

	protected $type        = 'product';
	private $default_width = 118;

	protected $used_settings = [
		'product_display_style',
		'display_label',
		'product_selection',
		'dynamic_products',
		'manual_products',
		'label_position',
		'button_width',
		'show_in_product_gallery',
	];

	protected $product_display_style = 'product';
	protected $display_label;
	protected $product_selection;
	protected $dynamic_products;
	protected $manual_products;
	protected $label_position;
	protected $button_width;
	protected $show_in_product_gallery;

	private $products = [];

	/**
	 * Render the HTML for the field.
	 */
	public function render(): void {
		$field_type = $this->type;

		add_action( "wc_product_options_after_{$field_type}_field", [ $this, 'add_button_width_css_variable' ] );

		$args = $this->get_product_type_args();

		if ( isset( $args['include'] ) && empty( $args['include'] ) ) {
			// The inclusion list is empty.
			// This is only possible if only one product was hand-picked
			// and that product is being visited.
			// We need to return here to avoid running a query
			// that will return 10 random products.
			return;
		}

		// get the products object
		$this->products = wc_get_products( $args );

		if ( empty( $this->products ) ) {
			return;
		}

		$this->run_qty_picker_setup_hooks();

		$this->render_field_wrap_open();

		$this->render_option_name();
		$this->render_products();
		$this->render_description();

		$this->render_field_wrap_close();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function render_option_name(): void {
		if ( $this->option->display_name ) {
			$name = sprintf(
				'<span class="wpo-option-name__text">%s</span>',
				esc_html( apply_filters( 'wc_product_options_get_output_string', $this->option->name, $this->option, 'option_name' ) )
			);

			/**
			 * Filters the prefix added to the option name.
			 *
			 * @param string $prefix The prefix string.
			 * @param Abstract_Field $field The current field object.
			 * @param WC_Product $product The current product object.
			 */
			$name_prefix = apply_filters( 'wc_product_options_option_name_prefix', '', $this, $this->get_product() );

			/**
			 * Filters the suffix added to the option name.
			 *
			 * @param string $suffix The suffix string.
			 * @param Abstract_Field $field The current field object.
			 * @param WC_Product $product The current product object.
			 */
			$name_suffix = apply_filters( 'wc_product_options_option_name_suffix', '', $this, $this->get_product() );

			$edit_button = '';

			if ( current_user_can( 'manage_woocommerce' ) ) {
				$edit_button = sprintf(
					'<span class="wpo-field-edit">%s</span>',
					$this->get_edit_option_link()
				);
			}

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			printf(
				'<p class="wpo-option-name">%1$s%2$s%3$s%4$s%5$s</p>',
				$edit_button,
				$name_prefix,
				$name,
				$this->get_required_symbol(),
				$name_suffix
			);
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Determines if the field has quantity pickers enabled.
	 *
	 * @return bool
	 */
	public function has_quantity_pickers(): bool {
		$default         = $this->product_display_style === 'product' ? 'spinner' : 'none';
		$has_qty_pickers = $this->get_quantity_pickers_setting( 'style', $default ) !== 'none';

		if ( intval( $this->get_quantity_pickers_setting( 'max', '' ) ) === 1 ) {
			$has_qty_pickers = false;
		}

		return apply_filters( 'wc_product_options_field_has_quantity_pickers', $has_qty_pickers, $this );
	}

	/**
	 * Get the HTML for the option quantity picker.
	 *
	 * @param int|null $product_id The product ID of the current choice
	 *
	 * @return string
	 */
	public function get_option_quantity_picker_html( $product_id = null ): string {
		if ( ! $this->has_quantity_pickers() ) {
			return $this->product_display_style === 'checkbox' ? '<span class="wpo-checkbox-inner"></span>' : '';
		}

		$input_name = $this->get_qty_input_name();
		$input_args = [
			'value' => $this->get_quantity_pickers_setting( 'value', 0 ),
			'min'   => $this->get_quantity_pickers_min(),
			'max'   => $this->get_quantity_pickers_max(),
			'step'  => $this->get_quantity_pickers_step(),
		];

		if ( $product_id ) {
			$input_name .= '[' . $product_id . ']';
			$input_args = $this->get_quantity_picker_restrictions( $product_id );
		}

		return sprintf(
			'<span class="wpo-quantity-picker wpo-quantity-picker-%2$s">
				%1$s
				<input class="wpo-qty-picker" type="number" name="%3$s" value="%4$s" min="%5$s" max="%6$s" step="%7$s" />
				%8$s
			</span>',
			$this->get_quantity_picker_spin_button_html( 'down' ),
			esc_attr( $this->get_quantity_pickers_style() ),
			esc_attr( $input_name ),
			esc_attr( $input_args['value'] ),
			esc_attr( $input_args['min'] ),
			esc_attr( $input_args['max'] > 0 ? $input_args['max'] : '' ),
			esc_attr( $input_args['step'] ),
			$this->get_quantity_picker_spin_button_html( 'up' )
		);
	}

	/**
	 * Add a style element with the CSS variable for the button width right after the field.
	 */
	public function add_button_width_css_variable(): void {
		if ( $this->product_display_style !== 'image_buttons' ) {
			return;
		}

		printf(
			'<style>div#%1$s .wpo-image-buttons{--wpo-image-buttons-width: %2$dpx;}</style>',
			esc_attr( $this->get_field_id() ),
			esc_attr( $this->get_button_width() )
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_field_attributes_array(): array {
		return array_merge(
			parent::get_field_attributes_array(),
			[
				'data-parent-type' => $this->type,
				'data-type'        => $this->product_display_style,
			]
		);
	}

	/**
	 * Render the HTML for the field input.
	 */
	private function render_products() {
		$single_product_service = wpo()->get_service( 'handlers/single_product' );

		if ( $single_product_service instanceof \Barn2\Plugin\WC_Product_Options\Handlers\Single_Product ) {
			remove_filter( 'woocommerce_get_price_suffix', [ $single_product_service, 'extend_price_suffix' ], PHP_INT_MAX );
		}

		$this->render_products_html_based_on_style();

		if ( $single_product_service instanceof \Barn2\Plugin\WC_Product_Options\Handlers\Single_Product ) {
			add_filter( 'woocommerce_get_price_suffix', [ $single_product_service, 'extend_price_suffix' ], PHP_INT_MAX, 2 );
		}
	}

	/**
	 * Render the HTML for products based on the style.
	 */
	protected function render_products_html_based_on_style() {
		switch ( $this->product_display_style ) {
			case 'checkbox':
				$this->render_checkboxes_style_html();
				break;
			case 'radio':
				$this->render_radios_style_html();
				break;
			case 'dropdown':
				$this->render_dropdowns_style_html();
				break;
			case 'image_buttons':
				$this->render_image_buttons_style_html();
				break;
			case 'product':
			default:
				$this->render_product_style_html();
				break;
		}
	}

	/**
	 * Render the HTML for the product style.
	 */
	protected function render_product_style_html() {
		$hide_out_of_stock_products = get_option( 'woocommerce_hide_out_of_stock_items' );

		$html  = '<div class="wpo-option-products">';
		$html .= sprintf(
			'<table id="%1$s" class="%2$s">',
			esc_attr( $this->get_input_id() ),
			'wpo-products-list'
		);

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

		ob_start();

		foreach ( $this->products as $product ) {
			// if product out of stock then return
			if ( ! $product->is_in_stock() && $hide_out_of_stock_products === 'yes' ) {
				continue;
			}

			// For variable products
			if ( $product->is_type( 'variable' ) ) {
				// display variable product
				echo $this->get_variable_product_style_product( $product );
			} else {
				// display simple products
				?>
			<tr class="wpo-product-option__list-product <?php echo ! $product->is_in_stock() ? 'wpo-product-out-of-stock' : ''; ?>">
			<!-- product thumbnail -->
				<td class="wpo-product-thumbnail">
					<a href="<?php echo get_permalink( $product->get_id() ); ?>" tabindex="0">
						<?php echo $product->get_image(); ?> 
					</a>
				</td>       
				<!-- product title -->
				<td class="wpo-product-name" data-title="Product">
					<a href="<?php echo get_permalink( $product->get_id() ); ?>" tabindex="0">
						<?php echo $product->get_title(); ?>
					</a>
				</td>
				<!-- product price -->
				<td class="wpo-product-price" data-title="Price"><?php echo $product->get_price_html(); ?></td>
				<td class="wpo-cart-button">
					<?php
						if ( $this->has_quantity_pickers() ) {
							echo $this->get_product_quantity_picker_html( $product->get_id() );
						}

						/**
						 * Filter the add to cart button text.
						 *
						 * @param string $add_to_cart_button_text The add to cart button text.
						 */
						$add_to_cart_button_text = \apply_filters( 'wc_product_options_product_add_to_cart_button_text', __( 'Add to cart', 'woocommerce-product-options' ) );

						printf(
							'<a href="%s" data-quantity="1" data-product-id="%s" class="button product_type_simple single_add_to_cart_button add_to_cart_button ajax_add_to_cart wp-element-button">%s</a>',
							'?add-to-cart=' . $product->get_id(),
							$product->get_id(),
							esc_html( $add_to_cart_button_text )
						);
					?>
				</td>
			</tr>
				<?php
			}
		}

		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

		$html .= ob_get_clean();
		$html .= '</table>';
		$html .= '</div>';

		// phpcs:reason This is escaped above.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped/
		echo $html;
	}

	/**
	 * Render products in checkbox styles
	 */
	protected function render_checkboxes_style_html() {
		$hide_out_of_stock_products = get_option( 'woocommerce_hide_out_of_stock_items' );

		$html = '<ul class="wpo-checkboxes wpo-checkboxes-one-col wpo-choice-list">';
		foreach ( $this->products as $index => $product ) {
			if ( ! $product->is_in_stock() && $hide_out_of_stock_products == 'yes' ) {
				continue;
			}
			if ( $product->is_type( 'variable' ) ) {
				// display variable product
				$html .= $this->get_variable_product_style_checkbox( $product, $index );
			} else {
				$html .= sprintf(
					'<li class="wpo-choice-item">
						<label class="wpo-checkbox" %7$s>
							<input type="checkbox" id="%1$s" name="%2$s[%3$s][]" value="%3$s" %8$s data-price-amount="%4$s" data-price-type="flat_fee" %10$s/>
							%9$s
							<div>
								%5$s
								%6$s
							</div>
						</label>
					</li>',
					esc_attr( sprintf( '%1$s-%2$s-%3$s', $this->get_input_id(), $index, $product->get_id() ) ),
					esc_attr( $this->get_input_name() ),
					esc_attr( $product->get_id() ),
					$product->get_price(),
					esc_html( $product->get_title() ),
					$product->get_price() ? sprintf( '<span class="wpo-price-container">(%s)</span>', $product->get_price_html() ) : '',
					! $product->is_in_stock() ? 'data-stock-out="true"' : '',
					disabled( ! $product->is_in_stock(), true, false ),
					$this->get_option_quantity_picker_html( $product->get_id() ),
					checked( ( $this->get_quantity_picker_restrictions( $product->get_id() )['value'] ?? 0 ) > 0, true, false )
				);
			}
		}

		$html .= '</ul>';

		// phpcs:reason This is escaped above.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped/
		echo $html;
	}

	/**
	 * Render products in radio styles
	 */
	protected function render_radios_style_html() {
		$hide_out_of_stock_products = get_option( 'woocommerce_hide_out_of_stock_items' );

		$html = '<ul class="wpo-radios wpo-radios-one-col wpo-choice-list">';

		foreach ( $this->products as $index => $product ) {
			if ( ! $product->is_in_stock() && $hide_out_of_stock_products == 'yes' ) {
				continue;
			}
			if ( $product->is_type( 'variable' ) ) {
				// display variable product
				$html .= $this->get_variable_product_style_radio( $product, $index );
			} else {
				$html .= sprintf(
					'<li class="wpo-choice-item">
						<label class="wpo-radio" %7$s>
							<input type="radio" id="%1$s" name="%2$s[%3$s][]" value="%3$s" %8$s data-price-amount="%4$s" data-price-type="flat_fee"/>
							<span class="wpo-radio-inner">
								<span class="wpo-radio-dot"></span>
							</span>
							<div>
								%5$s
								%6$s
							</div>
						</label>
					</li>',
					esc_attr( sprintf( '%1$s-%2$s', $this->get_input_id(), $index ) ),
					esc_attr( $this->get_input_name() ),
					esc_attr( $product->get_id() ),
					$product->get_price(),
					esc_html( $product->get_title() ),
					$product->get_price() ? sprintf( '<span class="wpo-price-container">(%s)</span>', $product->get_price_html() ) : '',
					! $product->is_in_stock() ? 'data-stock-out="true"' : '',
					disabled( ! $product->is_in_stock(), true, false )
				);
			}
			?>
			<?php
		}

		$html .= '</ul>';

		// phpcs:reason This is escaped above.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped/
		echo $html;
	}

	/**
	 * Render products in dropdown styles
	 */
	protected function render_dropdowns_style_html() {
		$hide_out_of_stock_products = get_option( 'woocommerce_hide_out_of_stock_items' );

		$max_qty  = $this->choice_qty['max'] ?? 1;
		$multiple = empty( $max_qty ) || $max_qty > 1 ? ' multiple' : '';

		$html  = '<div class="wpo-field-dropdown">';
		$html .= sprintf(
			'<select id="%1$s" name="%2$s[]" placeholder="%3$s"%4$s>',
			esc_attr( $this->get_input_id() ),
			esc_attr( $this->get_input_name() ),
			esc_attr__( 'Select a product', 'woocommerce-product-options' ),
			$multiple
		);

		$html .= '<option value="" hidden></option>';

		foreach ( $this->products as $index => $product ) {
			if ( ! $product->is_in_stock() && $hide_out_of_stock_products == 'yes' ) {
				continue;
			}
			if ( $product->is_type( 'variable' ) ) {
				$variations                         = $product->get_children();
				$manual_selected_product_variations = $this->get_manually_selected_product_variations( $product->get_id() );
				foreach ( $variations as $variation_id ) :
					if ( ! in_array( $variation_id, $manual_selected_product_variations, true ) ) {
						continue;
					}

					$variation    = wc_get_product( $variation_id );
					$out_of_stock = ! $variation->is_in_stock() || ! $variation->is_purchasable();

					$choice    = [
						'pricing'    => $variation->get_price(),
						'price_type' => 'flat_fee',
					];

					$variation_name = sprintf( '%1$s <span class="description">%2$s</span>', $variation->get_name(), wc_get_formatted_variation( $variation, true, false, true ) );
					$pricing        = $variation_name . ( $variation->get_price() ? sprintf( ' <span class="wpo-price-container">(%s)</span>', $variation->get_price_html() ) : '' );
					$data_display   = $pricing !== wp_strip_all_tags( $pricing ) ? 'data-display="' . esc_attr( $pricing ) . '"' : '';

					$html .= sprintf(
						'<option value="%1$s" %4$s %5$s data-product_id="%6$s" data-variation_id="%7$s" %8$s>%2$s%3$s</option>',
						esc_attr( $product->get_id() . ',' . $variation_id ),
						$variation_name,
						$variation->get_price() ? sprintf( ' (%s)', $variation->get_price_html() ) : '',
						disabled( $out_of_stock, true, false ),
						$this->get_choice_pricing_attributes( $choice ),
						$product->get_id(),
						$variation_id,
						$data_display
					);
				endforeach;
			} else {
				$choice = [
					'pricing'    => $product->get_price(),
					'price_type' => 'flat_fee',
				];

				$pricing      = $product->get_name() . ( $product->get_price() ? sprintf( ' <span class="wpo-price-container">(%s)</span>', $product->get_price_html() ) : '' );
				$data_display = $pricing !== wp_strip_all_tags( $pricing ) ? 'data-display="' . esc_attr( $pricing ) . '"' : '';

				$html .= sprintf(
					'<option value="%1$s" %4$s %5$s %6$s>%2$s%3$s</option>',
					esc_attr( $product->get_id() . ',' . $product->get_id() ),
					esc_html( $product->get_title() ),
					$product->get_price() ? sprintf( ' (%s)', $product->get_price_html() ) : '',
					disabled( ! $product->is_in_stock(), true, false ),
					$this->get_choice_pricing_attributes( $choice ),
					$data_display
				);
			}
		}

		$html .= '</select>';
		$html .= '</div>';

		// phpcs:reason This is escaped above.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped/
		echo $html;
	}

	/**
	 * Render products in image buttons styles
	 */
	protected function render_image_buttons_style_html() {
		$hide_out_of_stock_products = get_option( 'woocommerce_hide_out_of_stock_items' );

		if ( ! $this->display_label ) {
			$this->label_position = 'below';
		}

		$html = sprintf(
			'<ul class="wpo-image-buttons %s wpo-choice-list">',
			sprintf(
				'wpo-image-buttons-%s',
				$this->label_position ?? 'full'
			)
		);

		foreach ( $this->products as $product ) {
			if ( ! $product->is_in_stock() && $hide_out_of_stock_products == 'yes' ) {
				continue;
			}
			if ( $product->is_type( 'variable' ) ) {
				// display variable product
				$html .= $this->get_variable_product_style_image( $product );
			} else {
				$caption = $this->get_figcaption( $product );
				$html   .= sprintf(
					'<li class="wpo-choice-item">
						<label class="wpo-image-button" %9$s %11$s>
							<input type="checkbox" id="%1$s" name="%2$s[%3$s][]" value="%3$s" %10$s %13$s data-price-amount="%6$s" data-price-type="flat_fee">
							<figure class="%8$s">
								<div class="wpo-image-active">%7$s</div>
								%4$s
								%5$s
							</figure>
							%12$s
						</label>
					</li>',
					esc_attr( sprintf( '%1$s-%2$s', $this->get_input_id(), $product->get_id() ) ),
					esc_attr( $this->get_input_name() ),
					esc_attr( $product->get_id() ),
					$product->get_image(),
					$caption,
					$product->get_price(),
					$this->get_deselect_svg(),
					$this->get_image_wrap_class(),
					! $product->is_in_stock() ? 'data-stock-out="true"' : '',
					disabled( ! $product->is_in_stock(), true, false ),
					$this->get_image_data( $product->get_image_id() ),
					$this->get_option_quantity_picker_html( $product->get_id() ),
					checked( ( $this->get_quantity_picker_restrictions( $product->get_id() )['value'] ?? 0 ) > 0, true, false )
				);
			}
		}

		$html .= '</ul>';

		// phpcs:reason This is escaped above.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped/
		echo $html;
	}

	/**
	 * Display variations as indidivual products and allow them to directly add to cart
	 *
	 * @param  WC_Product $product
	 * @return string
	 */
	public function get_variable_product_style_product( $product ) {
		$variations                         = $product->get_children();
		$manual_selected_product_variations = $this->get_manually_selected_product_variations( $product->get_id() );

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

		ob_start();

		foreach ( $variations as $variation_id ) {
			if ( ! in_array( $variation_id, $manual_selected_product_variations, true ) ) {
				continue;
			}

			$variation = wc_get_product( $variation_id );

			?>
			<tr class="wpo-product-option__list-product <?php echo ( $variation->is_purchasable() && $variation->is_in_stock() ) ? '' : 'wpo-product-out-of-stock'; ?>">
			<!-- product thumbnail -->
				<td class="wpo-product-thumbnail">
					<a href="<?php echo get_permalink( $product->get_id() ); ?>" tabindex="0">
						<?php echo $variation->get_image(); ?>
					</a>
				</td>       
				<!-- product title -->
				<td class="wpo-product-name" data-title="Product">
					<a href="<?php echo get_permalink( $variation->get_id() ); ?>" tabindex="0">
						<?php echo $variation->get_name(); ?>
					</a>
				</td>
				<!-- product price -->
				<td class="wpo-product-price" data-title="Price"><?php echo $variation->get_price_html(); ?></td>
				<td class="wpo-cart-button">
					<?php
						if ( $this->has_quantity_pickers() ) {
							echo $this->get_product_quantity_picker_html( $variation->get_id() );
						}

						$add_to_cart_button_text = apply_filters( 'wc_product_options_product_add_to_cart_button_text', __( 'Add to cart', 'woocommerce-product-options' ) );

						// Display the add to cart button
						printf(
							'<a href="%s" data-quantity="1" class="button product_type_simple single_add_to_cart_button add_to_cart_button ajax_add_to_cart wp-element-button">%s</a>',
							'?add-to-cart=' . $variation->get_id(),
							esc_html( $add_to_cart_button_text ) // The add to cart text for the product
						);
					?>
				</td>
			</tr>
			<?php

		}

		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

		return ob_get_clean();
	}

	/**
	 * Return the variation products as checkboxes and allow them to add to cart from the product page
	 *
	 * @param WC_Product $product
	 * @return string
	 */
	public function get_variable_product_style_checkbox( $product, $product_index ) {
		$variations                         = $product->get_children();
		$manual_selected_product_variations = $this->get_manually_selected_product_variations( $product->get_id() );

		$html = '';

		foreach ( $variations as $variation_id ) {
			if ( ! in_array( $variation_id, $manual_selected_product_variations, true ) ) {
				continue;
			}

			$variation = wc_get_product( $variation_id );
			$out_of_stock = ! $variation->is_in_stock() || ! $variation->is_purchasable();

			$html .= sprintf(
				'<li class="wpo-choice-item">
					<label class="wpo-checkbox" %8$s>
						<input type="checkbox" id="%1$s" name="%2$s[%3$s][]" value="%4$s" %9$s data-price-amount="%5$s" data-price-type="flat_fee" />
						%10$s
						<div>
							%6$s
							%7$s
						</div>
					</label>
				</li>',
				esc_attr( sprintf( '%1$s-%2$s-%3$s', $this->get_input_id(), $product_index, $variation_id ) ),
				esc_attr( $this->get_input_name() ),
				esc_attr( $product->get_id() ),
				esc_attr( $variation_id ),
				$variation->get_price(),
				sprintf( '%1$s <span class="description">%2$s</span>', $variation->get_name(), wc_get_formatted_variation( $variation, true, false, true ) ),
				$variation->get_price() ? sprintf( ' <span class="wpo-price-container">(%s)</span>', $variation->get_price_html() ) : '',
				$out_of_stock ? 'data-stock-out="true"' : '',
				disabled( $out_of_stock, true, false ),
				$this->get_option_quantity_picker_html( $variation_id )
			);
		}

		return $html;
	}

	/**
	 * Return the variation products as radio buttons and allow them to add to cart from the product page
	 *
	 * @param WC_Product $product
	 * @param int $product_index The index of the item in the list of radio buttons
	 *
	 * @return string
	 */
	public function get_variable_product_style_radio( $product, $product_index ) {
		$variations                         = $product->get_children();
		$manual_selected_product_variations = $this->get_manually_selected_product_variations( $product->get_id() );

		$html = '';

		foreach ( $variations as $variation_id ) {
			if ( ! in_array( $variation_id, $manual_selected_product_variations, true ) ) {
				continue;
			}

			$variation = wc_get_product( $variation_id );
			$out_of_stock = ! $variation->is_in_stock() || ! $variation->is_purchasable();

			$html .= sprintf(
				'<li class="wpo-choice-item">
					<label class="wpo-radio" %8$s>
						<input type="radio" id="%1$s" name="%2$s[%3$s][]" value="%4$s" %9$s data-price-amount="%5$s" data-price-type="flat_fee"/>
						<span class="wpo-radio-inner">
							<span class="wpo-radio-dot"></span>
						</span>
						<div>
							%6$s
							%7$s
						</div>
					</label>
				</li>',
				esc_attr( sprintf( '%1$s-%2$s', $this->get_input_id(), $product_index ) ),
				esc_attr( $this->get_input_name() ),
				esc_attr( $product->get_id() ),
				esc_attr( $variation_id ),
				$variation->get_price(),
				sprintf( '%1$s <span class="description">%2$s</span>', $variation->get_name(), wc_get_formatted_variation( $variation, true, false, true ) ),
				$variation->get_price() ? sprintf( ' <span class="wpo-price-container">(%s)</span>', $variation->get_price_html() ) : '',
				$out_of_stock ? 'data-stock-out="true"' : '',
				disabled( $out_of_stock, true, false )
			);

		}

		return $html;
	}

	/**
	 * Return the variation products as image buttons and allow them to add to cart from the product page
	 *
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public function get_variable_product_style_image( $product ) {
		$variations                         = $product->get_children();
		$manual_selected_product_variations = $this->get_manually_selected_product_variations( $product->get_id() );

		$html = '';

		foreach ( $variations as $variation_id ) {
			if ( ! in_array( $variation_id, $manual_selected_product_variations, true ) ) {
				continue;
			}

			$variation    = wc_get_product( $variation_id );
			$caption      = $this->get_figcaption( $variation );
			$out_of_stock = ! $variation->is_in_stock() || ! $variation->is_purchasable();

			$html .= sprintf(
				'<li class="wpo-choice-item">
					<label class="wpo-image-button" %10$s %12$s>
						<input type="checkbox" id="%1$s" name="%2$s[%3$s][]" value="%4$s" %11$s data-price-amount="%7$s" data-price-type="flat_fee">
						<figure class="%9$s">
							<div class="wpo-image-active">%8$s</div>
							%5$s
							%6$s
						</figure>
						%13$s
					</label>
				</li>',
				esc_attr( sprintf( '%1$s-%2$s', $this->get_input_id(), $variation_id ) ),
				esc_attr( $this->get_input_name() ),
				esc_attr( $product->get_id() ),
				esc_attr( $variation_id ),
				$variation->get_image(),
				$caption,
				$variation->get_price(),
				$this->get_deselect_svg(),
				$this->get_image_wrap_class(),
				$out_of_stock ? 'data-stock-out="true"' : '',
				disabled( $out_of_stock, true, false ),
				$this->get_image_data( $variation->get_image_id() ),
				$this->get_option_quantity_picker_html( $variation->get_id() )
			);
		}

		return $html;
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitize( $value ) {
		if ( is_array( $value ) ) {
			$products = [];

			if ( is_string( $value['product_ids'] ) ) {
				$value['product_ids'] = explode( ',', $value['product_ids'] );
			}

			foreach ( $value['product_ids'] as $product_ids ) {
				if ( is_string( $product_ids ) ) {
					// Handle the case where both product ID and variation ID
					// are sent as a comma-separated string
					$product_ids = explode( ',', $product_ids );

					// There are two possible scenarios:
					// 1. This is not a variation, so the main product ID is sent twice
					//    e.g. "123,123"
					// 2. This is a variation, so the main product ID and variation ID
					//    are both sent e.g. "123,456"
					// In both cases, we only need the second ID
					// but we also add a safeguard to ensure the array
					// has more than one element
					if ( count( $product_ids ) > 1 ) {
						$product_ids = array_slice( $product_ids, 1 );
					}
				}

				$products = array_merge( $products, array_map( 'absint', $product_ids ) );
			}

			return array_unique( $products );
		}

		return sanitize_text_field( $value );
	}



	/**
	 * Get product variations that were manually selected in the backend options
	 *
	 * @param int   $product_id
	 *
	 * @return array
	 */
	public function get_manually_selected_product_variations( $product_id ) {
		$manual_product      = array_values(
			array_filter(
				(array) $this->manual_products,
				function ( $variable_product ) use ( $product_id ) {
					return $variable_product['product_id'] === $product_id;
				}
			)
		);
		$selected_variations = $manual_product[0]['variations'] ?? [];

		/**
		 * If no variations are selected, return an empty array
		 * This is to prevent variations with undefined attributes from being added to the cart.
		 */

		if ( empty( $selected_variations ) ) {
			$manual_product_object = wc_get_product( $manual_product[0]['product_id'] ?? 0 );

			if ( is_a( $manual_product_object, 'WC_Product_Variable' ) ) {
				foreach ( $manual_product_object->get_children() as $variation_id ) {
					$variation            = wc_get_product( $variation_id );
					$variation_attributes = array_filter(
						$variation->get_attributes(),
						function ( $attribute ) {
							return $attribute !== '';
						}
					);

					// If every variation attribute is defined, add the variation to the selected variations
					if ( empty( $variation_attributes ) ) {
						$selected_variations[] = [
							'id' => $variation_id,
						];
					}
				}
			}
		}

		return array_column( $selected_variations, 'id' );
	}

	/**
	 * Validate the product options and add to cart
	 *
	 * @param array $value
	 * @param array $option_data
	 * @return bool|WP_Error
	 */
	public function validate( $value, $option_data ) {
		/**
		 * Filters whether to use the cart quantity when adding product addons to the cart.
		 *
		 * @param bool        $use_cart_quantity Whether to use the cart quantity.
		 * @param Abstract_Field $field The product addons field instance.
		 * @param WC_Product  $product The WooCommerce product instance.
		 */
		$cart_quantity = Cart_Util::shall_addons_use_cart_quantity( $this->get_product() )
			? filter_input( INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT )
			: 1;

		$is_valid       = true;
		$products       = $value['product_ids'] ?? '';
		$cart_item_keys = $GLOBALS['wpo_last_added_cart_item_keys'] ?? [];

		if ( empty( $products ) ) {
			$products = $value;
		}

		if ( is_string( $products ) && ! empty( $products ) ) {
			// products is scalar (only from dropdown style)
			// so we need to convert it to array like all the other styles
			$products = array_map( 'intval', explode( ',', $products ) );
			$products = [
				$products[0] => [ $products[1] ?? 0 ],
			];
		}

		if ( is_array( $products ) ) {
			$products = array_map(
				function ( $product ) {
					if ( is_array( $product ) ) {
						return array_filter( array_map( 'absint', $product ) );
					}

					$product_ids = explode( ',', $product );
					$product     = $product_ids[0];

					if ( count( $product_ids ) > 1 ) {
						$product = $product_ids[1];
					}

					return absint( $product );
				},
				$products
			);

			$products = array_filter( $products );
		}

		if ( ! empty( $products ) ) {
			$item_data_handler = wpo()->get_service( 'handlers/item_data' );

			remove_filter( 'woocommerce_add_cart_item_data', [ $item_data_handler, 'add_cart_item_data' ], 10 );

			$is_associative_array = array_keys( $products ) !== range( 0, count( $products ) - 1 );

			foreach ( $products as $product_id => $variation_ids ) {
				// get the WC product object
				if ( ! $is_associative_array ) {
					$variation_ids = [ $variation_ids ];
				}

				$variation_ids = array_filter( array_map( 'absint', $variation_ids ) );

				foreach ( $variation_ids as $variation_id ) {
					$variation  = wc_get_product( $variation_id );
					$product_id = $variation ? $variation->get_parent_id() : 0;

					if ( ! $product_id && $variation ) {
						$product_id   = $variation_id;
						$variation_id = 0;
					}

					if ( ! $product_id ) {
						$is_valid = new WP_Error( 'wpo-validation-error', esc_html__( 'The selected product is not valid.', 'woocommerce-product-options' ) );
						break 2;
					}

					$product = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );

					if ( ! $product ) {
						$is_valid = new WP_Error( 'wpo-validation-error', esc_html__( 'The selected product is not valid.', 'woocommerce-product-options' ) );
						break 2;
					}

					if ( $product->is_purchasable() ) {
						if ( isset( $option_data[ "option-{$this->option->id}-qty" ] ) && is_array( $option_data[ "option-{$this->option->id}-qty" ] ) ) {
							$product_qty = intval( $option_data[ "option-{$this->option->id}-qty" ][ $product->get_id() ] ?? 1 );
						} else {
							$product_qty = intval( $option_data[ "option-{$this->option->id}-qty" ] ?? 1 );
						}

						$product_qty  *= $cart_quantity;
						$cart_item_key = WC()->cart->add_to_cart( $product->get_id(), $product_qty, (int) $variation_id );

						if ( $cart_item_key === false ) {
							// Remove previously added cart items for this option
							// or reduce the corresponding quantity by 1
							// as one of them failed to be added

							$is_valid = false;
							break 2;
						}

						$cart_item_keys[] = [
							'key' => $cart_item_key,
							'qty' => $product_qty,
						];
					}
				}
			}

			add_filter( 'woocommerce_add_cart_item_data', [ $item_data_handler, 'add_cart_item_data' ], 10, 4 );
		}

		if ( $is_valid === true ) {
			$GLOBALS['wpo_last_added_cart_item_keys'] = $cart_item_keys;

			if ( empty( $cart_item_keys ) ) {
				unset( $GLOBALS['wpo_last_added_cart_item_keys'] );
			}
		} else {
			foreach ( $cart_item_keys as $index => $item ) {
				$cart_item = WC()->cart->get_cart_item( $item['key'] );
				if ( $cart_item ) {
					if ( $cart_item['quantity'] > $item['qty'] ) {
						WC()->cart->set_quantity( $item['key'], $cart_item['quantity'] - $item['qty'], false );
					} else {
						WC()->cart->remove_cart_item( $item['key'] );
					}
				}
			}
		}

		return $this->validate_filters( $value, $option_data, $is_valid );
	}

	/**
	 * Gets the name attribute for the field input.
	 *
	 * @return string
	 */
	public function get_input_name(): string {
		return sprintf( 'wpo-option[option-%d][product_ids]', $this->option->id );
	}

	/**
	 * Get the class for the image wrap.
	 *
	 * @return string The class.
	 */
	private function get_image_wrap_class(): string {
		$class = 'wpo-image-wrap';

		return esc_attr( $class );
	}

	/**
	 * SVG for deselecting an image button.
	 *
	 * @return string
	 */
	private function get_deselect_svg(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M382-221.91 135.91-468l75.66-75.65L382-373.22l366.43-366.43L824.09-664 382-221.91Z"/></svg>';
	}

	/**
	 * Build product query args for product option type
	 *
	 * @return array $args
	 */
	public function get_product_type_args() {
		$product = $this->get_product();
		$args    = [];

		if ( $this->product_selection === 'dynamic' ) {
			// for dynamic products
			$orderby    = $this->get_product_orderby_arg_value( $this->dynamic_products['sort'] );
			$order      = $this->get_product_order_arg_value( $this->dynamic_products['sort'] );
			$categories = wp_list_pluck( (array) $this->dynamic_products['categories'], 'category_slug' );

			$args = [
				'exclude'     => [ $product->get_id() ],
				'type'        => 'simple',
				'orderby'     => $orderby,
				'order'       => $order,
				'limit'       => $this->dynamic_products['limit'] ?: get_option( 'posts_per_page' ),
				'category'    => $categories,
				'post_status' => 'publish',
			];

			if ( ! in_array( $orderby, [ 'title', 'date' ], true ) ) {
				$sorting          = [
					'price'      => '_price',
					'rating'     => '_wc_average_rating',
					'popularity' => 'total_sales',
				];
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = $sorting[ $orderby ];
			}
		} else {
			// for manually selected products
			$products_list = wp_list_pluck( $this->manual_products, 'product_id' );
			$products_list = array_diff( $products_list, [ $product->get_id() ] );

			$args = [
				'include'     => $products_list,
				'orderby'     => 'include',
				'post_status' => 'publish',
				'limit'       => count( $products_list ),
			];
		}

		if ( get_option( 'woocommerce_hide_out_of_stock_items' ) === 'yes' ) {
			$args['stock_status'] = 'instock';
		}

		return $args;
	}

	/**
	 * Get product orderby arg value
	 *
	 * @param string $sorting
	 *
	 * @return string
	 */
	public function get_product_orderby_arg_value( $sorting ) {
		return str_replace( [ 'asc', 'desc', '_' ], '', $sorting );
	}

	/**
	 * Get product orderby arg value
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function get_product_order_arg_value( $sorting ) {
		$order = 'desc';

		if ( str_contains( $sorting, 'asc' ) ) {
			$order = 'asc';
		}

		return strtoupper( $order );
	}

	private function get_figcaption( $product ) {
		$product_name = is_a( $product, 'WC_Product_Variation' ) ? sprintf( '%1$s <span class="description">%2$s</span>', $product->get_name(), wc_get_formatted_variation( $product, true, false, true ) ) : $product->get_name();
		$label        = $this->display_label ? $product_name : '';
		$label        = $label ? sprintf( '<span class="wpo-image-label">%s</span>', $label ) : '';
		$price        = $product->get_price() ? sprintf( '<span class="price wpo-price-container">%s</span>', $product->get_price_html() ) : '';

		if ( empty( $label ) && empty( $price ) ) {
			return '';
		}

		return sprintf(
			'<figcaption class="wpo-image-text">
				%1$s
				%2$s
			</figcaption>',
			$label,
			$price
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function stores_multiple_values(): bool {
		return in_array( $this->product_display_style, [ 'checkbox', 'image_buttons', 'text_labels' ], true );
	}

	/**
	 * Get the width of the image buttons.
	 *
	 * @since 1.6.4
	 * @return string
	 */
	private function get_button_width() {
		return esc_attr( $this->button_width ?: $this->default_width );
	}

	/**
	 * Whether the field is required.
	 *
	 * @return boolean
	 */
	public function is_required() {
		return parent::is_required() && $this->product_display_style !== 'product';
	}

	/**
	 * Get the HTML for the quantity picker for a product.
	 *
	 * @param int $addon_product_id The product ID.
	 * @return string
	 */
	public function get_product_quantity_picker_html( $addon_product_id ): string {
		$input_args = $this->get_quantity_picker_restrictions( $addon_product_id, true );

		return sprintf(
			'<div class="wpo-quantity-picker wpo-quantity-picker-%2$s">
				%1$s
				<input class="wpo-qty-picker" type="number" name="%3$s" value="%4$s" min="%5$s" max="%6$s" step="%7$s" />
				%8$s
			</div>',
			$this->get_quantity_picker_spin_button_html( 'down' ),
			esc_attr( $this->get_quantity_pickers_style() ),
			esc_attr( $this->get_qty_input_name() . '[' . $addon_product_id . ']' ),
			esc_attr( $input_args['value'] ),
			esc_attr( $input_args['min'] ),
			esc_attr( $input_args['max'] ),
			esc_attr( $input_args['step'] ),
			$this->get_quantity_picker_spin_button_html( 'up' )
		);
	}

	public function get_quantity_picker_restrictions( $addon_product_id, $filtered = false ) {
		$addon_product = wc_get_product( $addon_product_id );

		$value = $this->get_quantity_pickers_setting( 'value', 0 );
		$min   = $this->product_display_style === 'product' ? 1 : $this->get_quantity_pickers_min();
		$max   = $this->get_quantity_pickers_max() > 0 ? $this->get_quantity_pickers_max() : '';
		$step  = $this->get_quantity_pickers_step();

		if ( $value < $min ) {
			$value = $min;
		}

		$max_purchasable = $addon_product->get_max_purchase_quantity();

		if ( $max_purchasable > 0 && ( empty( $max ) || $max > $max_purchasable ) ) {
			$max = $max_purchasable;
		}

		$input_restrictions = compact( 'value', 'min', 'max', 'step' );

		if ( $filtered ) {
			/**
			 * Filter the restrictions used in the quantity picker
			 *
			 * @param int $input_restrictions The associative array with value, min, max and step
			 * @param self $field This field
			 * @param WC_Product $product The product this option is attached to
			 * @param int $addon_product The addon product the maximum quantity refers to
			 */
			$input_restrictions = apply_filters( 'wc_product_options_product_quantity_picker_restrictions', $input_restrictions, $this, $this->get_product(), $addon_product );
		}

		$input_restrictions = array_map( 'intval', $input_restrictions );

		if ( ! $addon_product->is_in_stock() ) {
			$input_restrictions = [
				'value' => 0,
				'min'   => 0,
				'max'   => 0,
				'step'  => 1,
			];
		}

		if ( $input_restrictions['max'] === 0 ) {
			$input_restrictions['max'] = '';
		}

		return $input_restrictions;
	}
}
