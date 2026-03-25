<?php
namespace Barn2\Plugin\WC_Product_Options\Handlers;

use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Service\Standard_Service;
use Barn2\Plugin\WC_Product_Options\Model\Group as Group_Model;
use Barn2\Plugin\WC_Product_Options\Model\Option as Option_Model;
use Barn2\Plugin\WC_Product_Options\Util\Util;
use Barn2\Plugin\WC_Product_Options\Util\Price as Price_Util;
use Barn2\Plugin\WC_Product_Options\Util\Display as Display_Util;
use Barn2\Plugin\WC_Product_Options\Dependencies\Lib\Registerable;

use function Barn2\Plugin\WC_Product_Options\wpo;

use WC_Product;
use WC_Product_Variable;

/**
 * Class to display the product options on the single product page.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Single_Product implements Registerable, Standard_Service {
	/**
	 * The current product.
	 *
	 * @var WC_Product|null
	 */
	private $product;

	private $product_cache;

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		$this->product_cache = wpo()->get_service( 'handlers/product_cache' );
		// initialize the hooks after product is set up
		add_action( 'template_redirect', [ $this, 'init' ] );
	}

	/**
	 * Initializes the hooks.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! is_product() ) {
			return;
		}

		// Only setup the hooks for the single product page if we have option groups.
		$this->setup_hooks();
	}

	/**
	 * Adds the necessary hooks to display the options.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'output_options' ], 30 );
		add_action( 'woocommerce_before_add_to_cart_quantity', [ $this, 'output_totals_container' ], 90 );

		add_filter( 'woocommerce_get_price_suffix', [ $this, 'extend_price_suffix' ], 1, 2 );

		add_filter( 'body_class', [ $this, 'add_body_class' ] );

		// image button gallery integration
		add_filter( 'woocommerce_product_get_gallery_image_ids', [ $this, 'add_image_button_images' ], 10, 2 );

		// variation attribute integration
		add_action( 'woocommerce_before_variations_form', [ $this, 'output_attribute_options' ], 10, 1 );

		/**
		 * Filters the priority for the `woocommerce_get_price_html` filter used to modify the main product price display.
		 *
		 * Many components modify the price HTML, so we use the highest priority to ensure our modification runs last
		 * but also allow developers to adjust this if needed.
		 *
		 * @param int $priority The priority for the filter.
		 */
		$get_price_html_priority = apply_filters( 'wc_product_options_get_price_html_priority', PHP_INT_MAX );
		add_filter( 'woocommerce_get_price_html', [ $this, 'price_display' ], $get_price_html_priority, 2 );
	}

	/**
	 * Adds image button images to the product gallery.
	 *
	 * @param array $image_ids
	 * @param \WC_Product $product
	 */
	public function add_image_button_images( $image_ids, $product ) {
		if ( empty( $this->image_options ) ) {
			return $image_ids;
		}

		$option_image_ids = array_map(
			function ( $option ) use ( $product ) {
				$class = Util::get_field_class( $option->type );

				if ( ! class_exists( $class ) ) {
					return [];
				}

				$option_model = Option_Model::where( 'id', $option->id )->get()->first();

				$field = new $class( $option_model, $product );

				if ( $field->is_variation_attribute_type_option() && ! $field->is_valid_attribute_option_for_product() ) {
					return [];
				}

				/**
				 * Filter to exclude image options from the product gallery.
				 *
				 * When an image option is set to switch the main product image to the choice image,
				 * this filter prevent those images from being added to the product gallery
				 * while still allowing the switch of the main product image to work.
				 *
				 * @since 2.5.1
				 * @param bool $exclude Whether to exclude the image options from the product gallery. Default false.
				 * @param object $option The option object.
				 * @param WC_Product $product The current product object.
				 */
				if ( apply_filters( 'wc_product_options_exclude_image_options_from_gallery', false, $option, $product ) ) {
					return [];
				}

				switch ( $option->type ) {
					case 'product':
						return array_filter( $this->get_product_choices_images( $option ) );
					case 'images':
					default:
						return array_column( $option->choices, 'media' );
				}
			},
			$this->product_cache->get_image_options()
		);

		if ( empty( $option_image_ids ) ) {
			return $image_ids;
		}

		return array_values( array_filter( array_unique( array_merge( $image_ids, array_merge( ...$option_image_ids ) ) ) ) );
	}

	/**
	 * Retrieves the images for the choices of a product option.
	 *
	 * @param object $option The option object.
	 * @return array An array of image IDs.
	 */
	public function get_product_choices_images( $option ) {
		if ( $option->type !== 'product' ) {
			return [];
		}

		$settings     = $option->settings;
		$product_type = 'manual';
		$args         = [];
		$product_id   = $this->product->get_id();

		if ( isset( $settings['product_selection'] ) && $settings['product_selection'] === 'dynamic' ) {
			$product_type = 'dynamic';
		}

		// for manually selected products
		if ( $product_type === 'manual' ) {
			$products_list = array_reduce(
				$settings['manual_products'],
				function ( $ids, $item ) {
					if ( $item['variations'] ) {
						return array_merge( $ids, wp_list_pluck( $item['variations'], 'id' ) );
					}

					return array_merge( $ids, [ $item['product_id'] ] );
				},
				[]
			);
			$product_ids   = array_diff( $products_list, [ $product_id ] );
		} elseif ( $product_type === 'dynamic' ) {
			// for dynamic products
			$dynamic_products = $settings['dynamic_products'];
			$order_by         = str_replace( [ 'asc', 'desc', '_' ], '', $dynamic_products['sort'] );
			$order            = strpos( $dynamic_products['sort'], 'desc' ) !== false ? 'desc' : 'asc';
			$categories       = wp_list_pluck( $dynamic_products['categories'], 'category_slug' );

			$args = [
				'exclude'  => [ $product_id ],
				'type'     => 'simple',
				'orderby'  => $order_by,
				'order'    => strtoupper( $order ),
				'limit'    => $dynamic_products['limit'],
				'category' => $categories,
				'return'   => 'ids',
			];

			$product_ids = wc_get_products( $args );
		}

		return array_map(
			function ( $product_id ) {
				$product = wc_get_product( $product_id );

				if ( $product && $product->is_visible() ) {
					return $product->get_image_id();
				}

				return 0;
			},
			$product_ids
		);
	}

	/**
	 * Options price totals container.
	 */
	public function output_totals_container() {
		$this->product_cache->set_product();

		if ( ! $this->product_cache->has_option_groups() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Display_Util::get_totals_container_html( $this->product_cache->get_product() );
	}

	/**
	 * Outputs the options on the single product page.
	 *
	 * @return void
	 */
	public function output_options() {
		$this->product_cache->set_product();

		if ( ! $this->product_cache->has_option_groups() ) {
			return;
		}

		// we handle this in the `output_attribute_options` method hooked to the `woocommerce_before_variations_form` action.
		if ( $this->product_cache->get_product() instanceof WC_Product_Variable && ! empty( $this->product_cache->get_attribute_options() ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Display_Util::get_groups_html( $this->product_cache->get_groups(), $this->product_cache->get_product() );
	}

	/**
	 * Filters the HTML of the price suffix to add the per product suffix if it exists.
	 *
	 * @param string $price_suffix The HTML of the default WooCommerce price suffix.
	 * @param \WC_Product $product The product.
	 * @return string The filtered HTML of the price suffix.
	 */
	public function extend_price_suffix( $price_suffix, $product ) {
		$this->product_cache->set_product( $product );

		if ( is_admin() || empty( $this->product_cache->get_formula_options() ) ) {
			return $price_suffix;
		}

		$price_suffixes = array_map(
			function ( $option ) {
				return $option->settings['price_suffix'] ?? '';
			},
			$this->product_cache->get_formula_options()
		);

		$suffix = reset( $price_suffixes );

		if ( empty( $suffix ) ) {
			return $price_suffix;
		}

		return sprintf(
			' <small class="wpo-price-suffix">%1$s</small>%2$s',
			esc_html( $suffix ),
			$price_suffix
		);
	}

	/**
	 * Adds a body class to the single product page if there are fields.
	 *
	 * @param array $classes
	 * @return array
	 */
	public function add_body_class( $classes ) {
		$classes[] = 'wpo-has-fields';

		return $classes;
	}

	/**
	 * H the variation attribute options dropdowns if we have custom WPO based attribute option.
	 *
	 * @return string
	 */
	public function output_attribute_options() {
		$this->product_cache->set_product();
		$attributes = $this->product_cache->get_attribute_options();

		if ( empty( $attributes ) ) {
			return;
		}

		$css = implode(
			'',
			array_map(
				function ( $attr ) {
					$attr = $attr->settings['selected_attribute'];

					return <<<CSS
						.variations {
							label[for="$attr"],
							select#$attr {
								display:none!important;
							}
						}
						tr:has(select#$attr) {
							/* Always hide the select element */
							display:none!important;
							th {display:none!important;}
							&:has(a.reset_variations) {display:table-row!important;}
						}
					CSS;
				},
				$attributes
			)
		);

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<style>%s</style>',
			$css
		);
		echo Display_Util::get_groups_html( $this->product_cache->get_groups(), $this->product_cache->get_product() );
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Outputs the main product price according to the price display settings.
	 *
	 * The price display format is determined by the price display setting of the groups assigned to the product.
	 * When more than one group is assigned to the product, the price display format with the highest priority is used.
	 * The priority order, from lowest to highest, is 'default', 'extend' and 'hide'.
	 *
	 * @param string $price_html The original price HTML.
	 * @param \WC_Product $product The product.
	 * @return string The modified price HTML.
	 */
	public function price_display( $price_html, $product ) {
		if (
			! is_product() ||
			( function_exists( 'wc_is_rest_api_request' ) && wc_is_rest_api_request() ) ||
			wp_doing_ajax() ||
			strpos( $price_html, 'wpo-price-display' ) !== false
		) {
			return $price_html;
		}

		/**
		 * Filter to determine whether to use the price display modification on the product page.
		 *
		 * @since 2.6.2
		 * @param bool $use_price_display Whether to use the price display modification. Default true.
		 * @param WC_Product $product The current product.
		 *
		 * @return bool
		 */
		$use_price_display = apply_filters( 'wc_product_options_use_single_product_price_display', true, $product );

		if ( ! $use_price_display ) {
			return $price_html;
		}

		// Only the *main* product, not related, upsells, blocks, etc.
		$main_id = get_queried_object_id();
		if ( ! $main_id || (int) $product->get_id() !== (int) $main_id ) {
			return $price_html;
		}

		if ( did_action( 'wc_product_options_before_option_container' ) > did_action( 'wc_product_options_after_option_container' ) ) {
			// The filter was called inside the options container, return original price html
			return $price_html;
		}

		$price_display_format = Price_Util::get_price_display_format( $this->product_cache->get_groups() );

		if ( $product->get_price() === 0 ) {
			/**
			 * Filters the price display format when the product price is zero.
			 *
			 * This filter allows to override the price display format when the product price is zero,
			 * regardless of the group settings.
			 *
			 * @param string $price_display_format The price display format.
			 * @param WC_Product $product The product.
			 */
			$price_display_format = apply_filters( 'wc_product_options_price_display_when_zero', $price_display_format, $product );
		}

		return sprintf(
			$price_display_format,
			$price_html
		);
	}
}
