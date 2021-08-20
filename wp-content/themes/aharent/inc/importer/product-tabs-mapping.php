<?php

/**
 * Register the 'Custom Tabs' column in the importer.
 *
 * @param array $options
 * @return array $options
 */
function add_column_to_importer($options)
{
	// column slug => column name
	$options['yikes_security_deposit'] = 'Security deposit';

	return $options;
}
add_filter('woocommerce_csv_product_import_mapping_options', 'add_column_to_importer');



/**
 * Add automatic mapping support for 'Custom Tabs'.
 * This will automatically select the correct mapping for columns named 'Custom Tabs'.
 *
 * @param array $columns
 * @return array $columns
 */
function add_column_to_mapping_screen($columns)
{
	// potential column name => column slug
	$columns['Security deposit'] = 'yikes_security_deposit';

	return $columns;
}
add_filter('woocommerce_csv_product_import_mapping_default_columns', 'add_column_to_mapping_screen');


/**
 * Process the data read from the CSV file
 * This just saves the decoded JSON in meta data, but you can do anything you want here with the data.
 *
 * @param WC_Product $object - Product being imported or updated.
 * @param array $data - CSV data read for the product.
 * @return WC_Product $object
 */
function process_import($object, $data)
{
	if (!empty($data['yikes_security_deposit'])) {
		$arr = json_decode($data['yikes_security_deposit'], true);
		$object->update_meta_data('yikes_woo_products_tabs', $arr);
	}

	return $object;
}
add_filter('woocommerce_product_import_pre_insert_product_object', 'process_import', 10, 2);


?>