<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['tables_min_id'] = array(
    'eshop_products_in_categories',
    'eshop_products_signs',
    'eshop_relevant_products',
    'system_panels_in_positions',
    'eshop_product_type_variables',
    'system_panel_type_variables',
    'system_page_type_variables',
    'system_list_type_variables',
    'system_pages_in_categories',
    'system_resource_rels',
    'user_users',
    
    'user_campaning_categories',
    'user_campaning_districts'
);

$config['table_cols']['id'] = 'id';
$config['table_cols']['order'] = '';
$config['table_cols']['order_cat'] = '';
$config['table_cols']['lang_id'] = 'lang_id';
$config['table_cols']['lastmod'] = 'lastmod';

/* User */

// user_regions
$config['table_config']['user_regions']['order'] = 'order';

// user_districts
$config['table_config']['user_districts']['order'] = 'order';

// user_categories
$config['table_config']['user_categories']['order'] = 'order';

// user_payment_types
$config['table_config']['user_payment_types']['order'] = 'order';

/* System & eshop */

/* Automatic */
$config['table_config']['user_list_type_data_{int}']['order'] = 'order';

// system_langs
$config['table_config']['system_langs']['order'] = 'order';

// admin_users
$config['table_config']['admin_users']['order'] = 'order';

// eshop_categories
$config['table_config']['eshop_categories']['order'] = 'order';
$config['table_config']['eshop_categories']['order_cat'] = 'parent_id';

// eshop_products
$config['table_config']['eshop_products']['order'] = 'order';

// eshop_product_types
$config['table_config']['eshop_product_types']['order'] = 'order';

// eshop_product_parameters
$config['table_config']['eshop_product_parameters']['order'] = 'order';

// eshop_product_type_variables
$config['table_config']['eshop_product_type_variables']['order'] = 'order';

// eshop_product_type_variable_values
$config['table_config']['eshop_product_type_variable_values']['order'] = 'order';

// eshop_product_parameter_groups
$config['table_config']['eshop_product_parameter_groups']['order'] = 'order';

// eshop_product_galleries
$config['table_config']['eshop_product_galleries']['order'] = 'order';

// eshop_product_gallery_images
$config['table_config']['eshop_product_gallery_images']['order'] = 'order';

// eshop_variants
$config['table_config']['eshop_variants']['order'] = 'order';

// eshop_variant_values
$config['table_config']['eshop_variant_values']['order'] = 'order';

// eshop_signs
$config['table_config']['eshop_signs']['order'] = 'order';

// eshop_customer_groups
$config['table_config']['eshop_customer_groups']['order'] = 'order';

// eshop_communications
$config['table_config']['eshop_communications']['order'] = 'order';

// eshop_transports
$config['table_config']['eshop_transports']['order'] = 'order';

// eshop_payments
$config['table_config']['eshop_payments']['order'] = 'order';

// eshop_order_states
$config['table_config']['eshop_order_states']['order'] = 'order';

// eshop_coupons
$config['table_config']['eshop_coupons']['order'] = 'order';

// eshop_currencies
$config['table_config']['eshop_currencies']['order'] = 'order';

// eshop_order_data
$config['table_config']['eshop_order_data']['order'] = 'order';

// system_positions
$config['table_config']['system_positions']['order'] = 'order';

// system_parts
$config['table_config']['system_parts']['order'] = 'order';

// system_panel_types
$config['table_config']['system_panel_types']['order'] = 'order';

// system_panel_type_variables
$config['table_config']['system_panel_type_variables']['order'] = 'order';

// system_panels
$config['table_config']['system_panels']['order'] = 'order';

// system_list_types
$config['table_config']['system_list_types']['order'] = 'order';

// system_list_type_variables
$config['table_config']['system_list_type_variables']['order'] = 'order';

// system_lists
$config['table_config']['system_lists']['order'] = 'order';

// system_page_types
$config['table_config']['system_page_types']['order'] = 'order';

// system_page_type_variables
$config['table_config']['system_page_type_variables']['order'] = 'order';

// system_pages
$config['table_config']['system_pages']['order'] = 'order';
$config['table_config']['system_pages']['order_cat'] = 'parent_page_id';

// system_menus
$config['table_config']['system_menus']['order'] = 'order';

// system_menu_links
$config['table_config']['system_menu_links']['order'] = 'order';
$config['table_config']['system_menu_links']['order_cat'] = 'parent_link_id';

// system_services
$config['table_config']['system_services']['order'] = 'order';
$config['table_config']['system_services']['order_cat'] = 'parent_service_id';

// system_categories
$config['table_config']['system_categories']['order'] = 'order';
$config['table_config']['system_categories']['order_cat'] = 'parent_id';

// system_themes
$config['table_config']['system_themes']['order'] = 'order';

// system_resources
$config['table_config']['system_resources']['order'] = 'order';
$config['table_config']['system_resources']['order_cat'] = 'theme_id';

// system_resource_rels
$config['table_config']['system_resource_rels']['order'] = 'order';
$config['table_config']['system_resource_rels']['order_cat'] = 'resource_id';

// system_domains
$config['table_config']['system_domains']['order'] = 'order';

// system_href_attributes
$config['table_config']['system_href_attributes']['order'] = 'order';

// system_email_wraps
$config['table_config']['system_email_wraps']['order'] = 'order';

// system_emails
$config['table_config']['system_emails']['order'] = 'order';

// system_labels
$config['table_config']['system_labels']['order'] = 'order';