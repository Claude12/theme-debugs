<?php
namespace Barn2\Plugin\WC_Product_Options\Model;

use Barn2\Plugin\WC_Product_Options\Dependencies\Illuminate\Database\Eloquent\Model;
use Barn2\Plugin\WC_Product_Options\Dependencies\Sematico\FluentQuery\Concerns\HasUniqueIdentifier;
use Barn2\Plugin\WC_Product_Options\Plugin;

/**
 * Representation of an individual checkout link.
 *
 * @package   Barn2\woocommerce-product-options
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Checkout_Link extends Model {

	use HasUniqueIdentifier;

	protected $table   = Plugin::META_PREFIX . 'checkout_links';
	public $timestamps = false;

	// phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	protected $primaryKey = 'session_key';


	/**
	 * Fields which can be mass assigned.
	 *
	 * @var array
	 */
	public $fillable = [
		'session_key',
		'session_value',
		'session_expiry',
		'created_at',
		'created_by',
		'last_accessed',
		'access_count',
	];

	/**
	 * Defaults
	 *
	 * @var array
	 */
	protected $attributes = [
		'access_count' => 0,
	];

	/**
	 * Automatically cast attributes in specific ways.
	 *
	 * @var array
	 */
	protected $casts = [
		'session_key'    => 'string',
		'session_value'  => 'string',
		'session_expiry' => 'integer',
		'created_at'     => 'datetime',
		'created_by'     => 'integer',
		'last_accessed'  => 'datetime',
		'access_count'   => 'integer',
	];
}
