<?php
namespace Barn2\Plugin\WC_Product_Options\Schema;

use Barn2\Plugin\WC_Product_Options\Plugin;
use Barn2\Plugin\WC_Product_Options\Dependencies\Illuminate\Database\Schema\Blueprint;

/**
 * Defines the groups database table schema.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Checkout_Links extends Base_Schema {

	public $table_name = Plugin::META_PREFIX . 'checkout_links';

	/**
	 * {@inheritdoc}
	 */
	public function create() {
		$this->db->schema()->create(
			$this->table_name,
			function ( Blueprint $table ) {
				$table->string( 'session_key', 32 );
				$table->longText( 'session_value' );
				$table->unsignedBigInteger( 'session_expiry' );
				$table->dateTime( 'created_at' )->useCurrent();
				$table->unsignedBigInteger( 'created_by' );
				$table->dateTime( 'last_accessed' )->nullable();
				$table->unsignedInteger( 'access_count' )->default( 0 );
				$table->primary( 'session_key' );
				$table->index( 'created_at' );
				$table->index( 'created_by' );
			}
		);
	}
}
