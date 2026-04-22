<?php

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Restrict for direct access
}
?>

<h1><?php echo esc_html__('Import Product Prices', 'addify_b2b'); ?></h1>

<div class="wrap">
	<form action="" method="post" enctype="multipart/form-data">

		<?php wp_nonce_field( 'afroleprice_import_action', 'afroleprice_import_nonce_field' ); ?>
		
		<p><?php echo esc_html__('CSV file must be in this format.', 'addify_b2b'); ?></p>

		<h3><?php echo esc_html__('File Format:', 'addify_b2b'); ?></h3>
		<table cellspacing="0" cellpadding="0" border="1" width="1100" class="format_table">
			<thead>
				<tr>
					<th><?php echo esc_html__('Product ID', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('SKU', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('Product Name', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('User Role', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('Customer Email', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('Quantity From(min qty)', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('Quantity To(max qty)', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('Adjustment/Discount Type', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('Discount Price/Value', 'addify_b2b'); ?></th>
					<th><?php echo esc_html__('Replace Original Price', 'addify_b2b'); ?></th>
				</tr>
				<tr>
					<td>1122</td>
					<td>test-product</td>
					<td>Test Product</td>
					<td></td>
					<td>vipcustomer@customer.com</td>
					<td>1</td>
					<td>1000</td>
					<td>fixed_price</td>
					<td>18</td>
					<td>yes</td>
				</tr>
				<tr>
					<td>1122</td>
					<td>test-product</td>
					<td>Test Product</td>
					<td>b2b</td>
					<td></td>
					<td>1</td>
					<td>100</td>
					<td>percentage_decrease</td>
					<td>18</td>
					<td>yes</td>
				</tr>
				<tr>
					<td>1122</td>
					<td>test-product</td>
					<td>Test Product</td>
					<td>b2b</td>
					<td></td>
					<td>101</td>
					<td>1000</td>
					<td>percentage_decrease</td>
					<td>16</td>
					<td>yes</td>
				</tr>
				<tr>
					<td>1122</td>
					<td>test-product</td>
					<td>Test Product</td>
					<td>wholesale</td>
					<td></td>
					<td>0</td>
					<td>0</td>
					<td>fixed_price</td>
					<td>19</td>
					<td>no</td>
				</tr>
			</thead>
		</table>
		<div class="csvFromatData">
			<h3><?php echo esc_html__('Instructions:', 'addify_b2b'); ?></h3>
			<ul>
			<li><b><?php echo esc_html__('Product ID: ', 'addify_b2b'); ?></b><?php echo esc_html__('Unique product identifier, this is required as the extension will import prices based on product and variation IDs.', 'addify_b2b'); ?></li>

			<li><b><?php echo esc_html__('SKU: ', 'addify_b2b'); ?></b><?php echo esc_html__('Stock keeping unit can be added as the second column for your reference as it won’t be used by extension while importing.', 'addify_b2b'); ?></li>
			<li><b><?php echo esc_html__('Product Name: ', 'addify_b2b'); ?></b><?php echo esc_html__('Product name can be added as the third column for reference. Just like SKU this won’t be used by extension while importing prices.', 'addify_b2b'); ?></li>
			<li><b><?php echo esc_html__('User Role: ', 'addify_b2b'); ?>:</b><?php echo esc_html__('Please add the user role ID/Value. For example the admin user roles is usually administrator, Shop Manager has shop_manager. No Caps and spaces!', 'addify_b2b'); ?></li>
			<li><b><?php echo esc_html__('Customer Email: ', 'addify_b2b'); ?></b><?php echo esc_html__('Add email address of the customer for which might want to add prices. If you need to import prices by user role any, please leave this empty.', 'addify_b2b'); ?></li>
			<li><b><?php echo esc_html__('Quantity From: ', 'addify_b2b'); ?>:</b><?php echo esc_html__('This is the minimum required quantity number to add prices. If you want to add same price for no matter the quantity, please leave this empty.', 'addify_b2b'); ?></li>
			<li><b><?php echo esc_html__('Quantity To: ', 'addify_b2b'); ?></b><?php echo esc_html__('This is the maximum quantity at the prices will be applicable. If you want to add same price for no matter the quantity, please leave this empty.', 'addify_b2b'); ?></li>
			<li><b><?php echo esc_html__('Adjustment Type: ', 'addify_b2b'); ?>:</b><?php echo esc_html__('Please use the values as fixed_price, fixed_increase, fixed_decrease, percentage_decrease, percentage_increase.', 'addify_b2b'); ?></li>
			<li><b><?php echo esc_html__('Discount Price: ', 'addify_b2b'); ?></b><?php echo esc_html__('The value will be considered as percentage value when percentage price adjustment is applied and when its fixed price, fixed increase or decrease, this will be considered as a number value.', 'addify_b2b'); ?></li>
			<li><b><?php echo esc_html__('Replace Original Price: ', 'addify_b2b'); ?></b><?php echo esc_html__('Choose to replace to display new price as special price along with existing one or just show the new price by replacing the existing one.', 'addify_b2b'); ?></li>
			</ul>
		
			
			<h3 class="imp_msg"><?php echo esc_html__('Cautions:', 'addify_b2b'); ?></h3>
			<ul>
				<li><?php echo esc_html__('When importing the existing prices will be removed. For example, if the import sheet includes prices for Album, the existing role based prices for album will be removed and the new prices will be added using CSV.', 'addify_b2b'); ?></li>
				<li><?php echo esc_html__('Please do not add empty rows like if you are not adding price for specific product, please remove its row.', 'addify_b2b'); ?></li>
				<li><?php echo esc_html__('Import prices using CSV file only.', 'addify_b2b'); ?></li>
			</ul>
		</div>

		<div class="csp_import_prices_div2">
			<label><?php echo esc_html__('Upload CSV File', 'addify_b2b'); ?></label>
			<input type="file" name="afb2b_import_csv_file" id="afb2b_import_csv_file" onchange="validate_fileupload(this);">
		</div>

		<div class="csp_import_prices_div2">
			<div id="feedback" style="color: red;"></div>
			<p><input type="submit" name="afb2b_import_prices" id="afb2b_import_prices" class="button button-primary" value="<?php echo esc_html__('Import CSV', 'addify_b2b'); ?>" onclick="return valid_form();"></p>
		</div>

	</form>
</div>

<script>
var valid = false;

function validate_fileupload(input_element)
{
	var el = document.getElementById("feedback");
	var fileName = input_element.value;
	var allowed_extensions = new Array("csv");
	var file_extension = fileName.split('.').pop();
	for(var i = 0; i < allowed_extensions.length; i++)
	{
		if(allowed_extensions[i]==file_extension)
		{
			valid = true; // valid file extension
			el.innerHTML = "";
			return;
		}
	}
	el.innerHTML='<?php echo esc_html__('Invalid file type, Only csv file is allowed.', 'addify_b2b'); ?>';
	valid = false;
}

function valid_form()
{
	var afupfile = jQuery('#afb2b_import_csv_file').val();
	if ('' == afupfile) {

		jQuery('#feedback').html('<?php echo esc_html__('Please upload a CSV file.', 'addify_b2b'); ?>');
		return false;

	} else {

		return valid;
	}
	
}
</script>
