<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('products');
        $this->cms->model->load_eshop('product_parameters');
        $this->cms->model->load_eshop('product_parameter_groups');
        $this->cms->model->load_eshop('product_parameter_data');
        $this->cms->model->load_eshop('variants');
        $this->cms->model->load_eshop('variant_values');
        $this->cms->model->load_eshop('taxes');
        $this->cms->model->load_eshop('manufacturers');
        $this->cms->model->load_eshop('distributors');
        $this->cms->model->load_eshop('product_types');
        $this->cms->model->load_eshop('product_type_variables');
        $this->cms->model->load_eshop('product_type_variable_values');
        $this->cms->model->load_eshop('product_galleries');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add#tab-1', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_6'));
        $this->admin->form->col(__('col_7'));
        $this->admin->form->col(__('col_8'));
        $this->admin->form->col(__('col_9'));
        $this->admin->form->col(__('col_10'));
        $this->admin->form->col(__('col_11'));
        
        foreach($this->e_products_model->get_data() as $product)
        {
            $options_cell = '';
            $options_cell .= '<a href="' . href_product($product->id) . '">' . __('button_11') . '</a>';
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $product->id, __('button_2'), __('confirm_1'));
            
            $this->admin->form->cell(admin_anchor('~/edit/' . $product->id, $product->_name));
            $this->admin->form->cell(parse_price($product->_price));
            $this->admin->form->cell(admin_anchor('eshop/taxes/edit/' . $product->tax_id, $this->e_taxes_model->get_item_data($product->tax_id, 'name') . ' (' . $this->e_taxes_model->get_item_data($product->tax_id, 'tax') . '%)'));
            $this->admin->form->cell((intval($product->product_gallery_id) > 0) ? admin_anchor('eshop/product_galleries/edit/' . $product->product_gallery_id, $this->e_product_galleries_model->get_item_data($product->product_gallery_id, '_name')) : '');
            $this->admin->form->cell($product->quantity);
            $this->admin->form->cell($product->sku);
            $this->admin->form->cell($product->ean);
            $this->admin->form->cell($this->admin->form->cell_checkbox((($product->index) ? '~/unindex_product/' : '~/index_product/') . $product->id, (($product->index) ? __('button_9') : __('button_10')), $product->index));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($product->public) ? '~/unpublish_product/' : '~/publish_product/') . $product->id, (($product->public) ? __('button_7') : __('button_8')), $product->public));
            $this->admin->form->cell($this->admin->form->cell_indicator($product->sitemap_priority, 1));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $product->id), 'edit');
            $contextmenu[] = array(__('button_11'), href_product($product->id), 'show');
            
            if($product->index) $contextmenu[] = array(__('button_9'), admin_url('~/unindex_product/' . $product->id), 'noindex');
            else $contextmenu[] = array(__('button_10'), admin_url('~/index_product/' . $product->id), 'index');
            
            if($product->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_product/' . $product->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/publish_product/' . $product->id), 'check');
            
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $product->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($product->id, NULL, NULL, NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function unindex_product($product_id = '')
    {
        if($this->e_products_model->item_exists($product_id))
        {
            $this->e_products_model->set_item_data($product_id, array('index' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_6'), TRUE);
        }
        admin_redirect();
    }
    
    function index_product($product_id = '')
    {
        if($this->e_products_model->item_exists($product_id))
        {
            $this->e_products_model->set_item_data($product_id, array('index' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_7'), TRUE);
        }
        admin_redirect();
    }
    
    function unpublish_product($product_id = '')
    {
        if($this->e_products_model->item_exists($product_id))
        {
            $this->e_products_model->set_item_data($product_id, array('public' => FALSE));
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_8'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_product($product_id = '')
    {
        if($this->e_products_model->item_exists($product_id))
        {
            $this->e_products_model->set_item_data($product_id, array('public' => TRUE));
            $this->admin->form->message(__('message_7'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_9'), TRUE);
        }
        admin_redirect();
    }
    
    function add()
    {
        $this->admin->form->warning(__('warning_1'));
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            // Add product
            $product_data = array();
            
            $product_data['_name'] = $this->input->post('_name');
            $product_data['_alias'] = $this->input->post('_alias');
            $product_data['_price'] = $this->input->post('_price');
            $product_data['quantity'] = $this->input->post('quantity');
            $product_data['_description'] = $this->input->post('_description');
            $product_data['public'] = is_form_true($this->input->post('public'));
            $product_data['sku'] = $this->input->post('sku');
            $product_data['ean'] = $this->input->post('ean');
            $product_data['tax_id'] = $this->input->post('tax_id');
            $product_data['manufacturer_id'] = $this->input->post('manufacturer_id');
            $product_data['distributor_id'] = $this->input->post('distributor_id');
            $product_data['_meta_title'] = $this->input->post('_meta_title');
            $product_data['_meta_description'] = $this->input->post('_meta_description');
            $product_data['_meta_keywords'] = $this->input->post('_meta_keywords');
            $product_data['changefreq'] = $this->input->post('changefreq');
            $product_data['index'] = ($this->input->post('index') == cfg('form', 'true'));
            $product_data['sitemap_priority'] = $this->input->post('sitemap_priority');
            $product_data['tpl'] = $this->input->post('tpl');
            $product_data['product_gallery_id'] = $this->input->post('product_gallery_id');
            $product_data['image'] = $this->input->post('image');
            
            if(strlen($product_data['_alias']) == 0) $product_data['_alias'] = url_title($product_data['_name']);
            
            $this->e_products_model->add_item($product_data);
            $product_id = $this->e_products_model->insert_id();
            
            // Add to categories
            $status = TRUE;
            
            $in_categories = $this->input->post('in_categories');
            
            if(is_array($in_categories))
            {
                foreach($in_categories as $category_id)
                {
                    if($this->eshop->categories->category_exists($category_id)) $this->eshop->products->add_product_to_category($product_id, $category_id);
                    else $status = FALSE;
                }
            }
            
            // Set relevant products
            $relevant_products = $this->input->post('relevant_products');
            if(is_array($relevant_products))
            {
                if(!$this->eshop->products->set_relevant_products($product_id, $relevant_products)) $status = FALSE;
            }
            
            if($status) $this->admin->form->message(__('message_1'), TRUE);
            else $this->admin->form->error(__('error_3'), TRUE);
            
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('input', '_alias', __('field_2'));
        $this->admin->form->add_field('input', '_price', __('field_3'));
        $this->admin->form->add_field('input', 'quantity', __('field_4'));
        $this->admin->form->add_field('multiple', 'in_categories', __('field_19'), $this->eshop->categories->get_categories_select_data());
        $this->admin->form->add_field('multiple', 'relevant_products', __('field_23'), $this->eshop->products->get_relevant_products_select_data());
        $this->admin->form->add_field('imagepicker', 'image', __('field_27'));
        $this->admin->form->add_field('product_gallery', 'product_gallery_id', __('field_26'), '', TRUE);
        $this->admin->form->add_field('ckeditor', '_description', __('field_5'));
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('checkbox', 'public', __('field_6'), TRUE);
        $this->admin->form->add_field('input', 'sku', __('field_7'));
        $this->admin->form->add_field('input', 'ean', __('field_8'));
        $this->admin->form->add_field('select', 'tax_id', __('field_9'), $this->e_taxes_model->get_data_in_col('name'), NULL, TRUE);
        $this->admin->form->add_field('select', 'manufacturer_id', __('field_10'), $this->e_manufacturers_model->get_data_in_col('name'), NULL, TRUE);
        $this->admin->form->add_field('select', 'distributor_id', __('field_11'), $this->e_distributors_model->get_data_in_col('name'), NULL, TRUE);
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('input', '_meta_title', __('field_12'), '', __('placeholder_1'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_13'), '', __('placeholder_2'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_14'), '', __('placeholder_2'));
        $this->admin->form->add_field('select_changefreq', 'changefreq', __('field_15'), cfg('changefreq', 'default'));
        $this->admin->form->add_field('checkbox', 'index', __('field_16'), TRUE);
        $this->admin->form->add_field('slider', 'sitemap_priority', __('field_17'), cfg('sitemap', 'default_priority'), 0, 1, 0.1);
        
        $this->admin->form->tab(__('tab_7'));
        $this->admin->form->add_field('select', 'tpl', __('field_18'), $this->cms->templates->get_templates_select_data('products', TRUE));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($product_id = '')
    {
        if(!$this->e_products_model->item_exists($product_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        $this->_validation_product_type_variables($this->input->post('product_type_id'));
        $this->_validation_variants($product_id);
        
        if($this->admin->form->validate())
        {
            $product_data = array();
            
            $product_data['_name'] = $this->input->post('_name');
            $product_data['_alias'] = $this->input->post('_alias');
            $product_data['_price'] = $this->input->post('_price');
            $product_data['quantity'] = $this->input->post('quantity');
            $product_data['_description'] = $this->input->post('_description');
            $product_data['public'] = is_form_true($this->input->post('public'));
            $product_data['sku'] = $this->input->post('sku');
            $product_data['ean'] = $this->input->post('ean');
            $product_data['tax_id'] = $this->input->post('tax_id');
            $product_data['manufacturer_id'] = $this->input->post('manufacturer_id');
            $product_data['distributor_id'] = $this->input->post('distributor_id');
            $product_data['_meta_title'] = $this->input->post('_meta_title');
            $product_data['_meta_description'] = $this->input->post('_meta_description');
            $product_data['_meta_keywords'] = $this->input->post('_meta_keywords');
            $product_data['changefreq'] = $this->input->post('changefreq');
            $product_data['index'] = ($this->input->post('index') == cfg('form', 'true'));
            $product_data['sitemap_priority'] = $this->input->post('sitemap_priority');
            $product_data['tpl'] = $this->input->post('tpl');
            $product_data['product_parameter_group_id'] = $this->input->post('product_parameter_group_id');
            $product_data['product_type_id'] = $this->input->post('product_type_id');
            $product_data['product_gallery_id'] = $this->input->post('product_gallery_id');
            $product_data['image'] = $this->input->post('image');
            
            if(strlen($product_data['_alias']) == 0) $product_data['_alias'] = url_title($product_data['_name']);
            
            $this->e_products_model->set_item_data($product_id, $product_data);
            
            // Add to categories
            $status = TRUE;
            $product_category_ids = array();
            $in_categories = $this->input->post('in_categories');
            if(is_array($in_categories))
            {
                foreach($in_categories as $category_id)
                {
                    if($this->eshop->categories->category_exists($category_id)) $product_category_ids[] = $category_id;
                    else $status = FALSE;
                }
            }
            
            $this->eshop->products->set_product_categories($product_id, $product_category_ids);
            
            // Edit parameters
            if(!db_config_bool('remember_product_parameters')) $this->eshop->parameters->delete_product_parameters($product_id);
            $product_parameter_group_id = $this->input->post('product_parameter_group_id');
            $parameter_ids = array();
            if($this->e_product_parameter_groups_model->item_exists($product_parameter_group_id))
            {
                $parameter_ids = $this->eshop->parameters->get_parameter_ids_in_group($product_parameter_group_id);
                foreach($parameter_ids as $parameter_id)
                {
                    $this->eshop->parameters->set_product_parameter($product_id, $parameter_id, $this->input->post('_parameter_' . $parameter_id));
                }
            }
            
            // Edit product type variables
            if(!db_config_bool('remember_product_variables')) $this->eshop->product_types->delete_product_variable_values($product_id);
            foreach($this->eshop->product_types->get_variable_ids($this->input->post('product_type_id')) as $product_type_variable_id)
            {
                if($this->eshop->product_types->set_product_variable_value($product_id, $product_type_variable_id, $this->input->post('product_type_variable_' . $product_type_variable_id)) === FALSE) $status = FALSE;
            }
            
            // Edit variants
            $variant_ids = $this->input->post('variant_ids');
            if(!is_array($variant_ids)) $variant_ids = array();
            if(!$this->eshop->variants->set_product_variants($product_id, $variant_ids)) $status = FALSE;
            
            // Edit variant data
            if(!db_config_bool('remember_product_variants')) $this->eshop->variants->delete_all_product_variant_data($product_id);
            $variant_data = array();
            
            foreach($this->eshop->variants->get_variant_combinations((array)$this->input->post('variant_ids')) as $variant)
            {
                // Edit product variant data
                $variant = $this->eshop->variants->array2string($variant);
                
                $variant_data[$variant]['_price'] = $this->input->post('_price_variant_' . $variant);
                $variant_data[$variant]['quantity'] = $this->input->post('quantity_variant_' . $variant);
                $variant_data[$variant]['ean'] = $this->input->post('ean_variant_' . $variant);
                $variant_data[$variant]['sku'] = $this->input->post('sku_variant_' . $variant);
                $variant_data[$variant]['image'] = $this->input->post('image_variant_' . $variant);
                $variant_data[$variant]['product_gallery_id'] = $this->input->post('product_gallery_id_variant_' . $variant);
                
                $this->eshop->variants->set_product_variant_data($product_id, $variant, $variant_data[$variant]);
                
                // Edit product variant parameter
                if(!db_config_bool('remember_product_variant_parameters')) $this->eshop->parameters->delete_product_variant_parameters($product_id, $variant);
                foreach($parameter_ids as $parameter_id)
                {
                    $this->eshop->parameters->set_product_variant_parameter($product_id, $variant, $parameter_id, $this->input->post('_variant_parameter_' . $parameter_id . '_' . $variant));
                }
            }
            
            // Set product signs
            if(!$this->eshop->products->set_product_signs($product_id, $this->input->post('sign_ids'))) $status = FALSE;
            
            // Set relevant products
            if(!$this->eshop->products->set_relevant_products($product_id, (array)$this->input->post('relevant_products'))) $status = FALSE;
            
            if($status) $this->admin->form->message(__('message_2'), url_param() != 'accept');
            else $this->admin->form->error(__('error_4'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->hidden_fields['product_id'] = $product_id;
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_products_model->$product_id->_name);
        $this->admin->form->add_field('input', '_alias', __('field_2'), $this->e_products_model->$product_id->_alias);
        $this->admin->form->add_field('input', '_price', __('field_3'), doubleval($this->e_products_model->$product_id->_price));
        $this->admin->form->add_field('input', 'quantity', __('field_4'), $this->e_products_model->$product_id->quantity);
        $this->admin->form->add_field('multiple', 'in_categories', __('field_19'), $this->eshop->categories->get_categories_select_data(), $this->eshop->products->get_product_categories($product_id));
        $this->admin->form->add_field('multiple', 'relevant_products', __('field_23'), $this->eshop->products->get_relevant_products_select_data($product_id), $this->eshop->products->get_relevant_product_ids($product_id));
        $this->admin->form->add_field('imagepicker', 'image', __('field_27'), $this->e_products_model->$product_id->image);
        $this->admin->form->add_field('product_gallery', 'product_gallery_id', __('field_26'), $this->e_products_model->$product_id->product_gallery_id, TRUE);
        $this->admin->form->add_field('ckeditor', '_description', __('field_5'), $this->e_products_model->$product_id->_description);
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('multiple', 'sign_ids', __('field_22'), $this->eshop->products->get_signs_select_data(), $this->eshop->products->get_product_sign_ids($product_id));
        $this->admin->form->add_field('checkbox', 'public', __('field_6'), (bool)$this->e_products_model->$product_id->public);
        $this->admin->form->add_field('input', 'sku', __('field_7'), $this->e_products_model->$product_id->sku);
        $this->admin->form->add_field('input', 'ean', __('field_8'), $this->e_products_model->$product_id->ean);
        $this->admin->form->add_field('select', 'tax_id', __('field_9'), $this->e_taxes_model->get_data_in_col('name'), $this->e_products_model->$product_id->tax_id, TRUE);
        $this->admin->form->add_field('select', 'manufacturer_id', __('field_10'), $this->e_manufacturers_model->get_data_in_col('name'), $this->e_products_model->$product_id->manufacturer_id, TRUE);
        $this->admin->form->add_field('select', 'distributor_id', __('field_11'), $this->e_distributors_model->get_data_in_col('name'), $this->e_products_model->$product_id->distributor_id, TRUE);
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('input', '_meta_title', __('field_12'), $this->e_products_model->$product_id->_meta_title, __('placeholder_1'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_13'), $this->e_products_model->$product_id->_meta_description, __('placeholder_2'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_14'), $this->e_products_model->$product_id->_meta_keywords, __('placeholder_2'));
        $this->admin->form->add_field('select_changefreq', 'changefreq', __('field_15'), $this->e_products_model->$product_id->changefreq);
        $this->admin->form->add_field('checkbox', 'index', __('field_16'), (bool)$this->e_products_model->$product_id->index);
        $this->admin->form->add_field('slider', 'sitemap_priority', __('field_17'), $this->e_products_model->$product_id->sitemap_priority, 0, 1, 0.1);
        
        $this->admin->form->tab(__('tab_4'));
        $this->admin->form->add_field('select', 'product_parameter_group_id', __('field_20'), $this->e_product_parameter_groups_model->get_data_in_col('name'), $this->e_products_model->$product_id->product_parameter_group_id, TRUE);
        $this->admin->form->ajax_area('ajax_parameters', array('product_parameter_group_id'));
        
        $this->admin->form->tab(__('tab_5'));
        $this->admin->form->add_field('select', 'product_type_id', __('field_21'), $this->eshop->product_types->get_product_types_select_data(), $this->e_products_model->$product_id->product_type_id, TRUE);
        $this->admin->form->ajax_area('ajax_product_type', array('product_type_id'));
        
        $this->admin->form->tab(__('tab_6'));
        $this->admin->form->add_field('multiple', 'variant_ids', __('field_24'), $this->eshop->variants->get_available_variants_select_data(), $this->eshop->variants->get_product_variants($product_id));
        $this->admin->form->ajax_area('ajax_variants', array('variant_ids[]'));
        
        $this->admin->form->tab(__('tab_7'));
        $this->admin->form->add_field('select', 'tpl', __('field_18'), $this->cms->templates->get_templates_select_data('products', TRUE), $this->e_products_model->$product_id->tpl);
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_link(site_url(href_product($product_id)), __('button_11'), 'home');
        $this->admin->form->button_index();
        
        $this->admin->form->button_helper(__('helper_1'), __('helper_title_1'));
        
        $this->admin->form->generate();
    }
    
    function ajax_parameters()
    {
        if(!$this->input->is_ajax_request()) show_404();
        
        $data = array();
        
        $this->admin->form->field_id_prefix = 'parameter_';
        
        $product_parameter_group_id = $this->input->post('product_parameter_group_id');
        
        if(strlen($product_parameter_group_id) == 0)
        {
            $data['result'] = TRUE;
            $data['content'] = '';
        }
        else
        {
            $data['result'] = $this->e_product_parameter_groups_model->item_exists($product_parameter_group_id);
            $data['content'] = '';
            $data['error'] = __('error_10');

            if($data['result'])
            {
                $this->e_product_parameters_model->where('product_parameter_group_id', '=', $product_parameter_group_id);
                foreach($this->e_product_parameters_model->get_data() as $parameter)
                {
                    $data['content'] .= $this->admin->form->get_field_row('input', '_parameter_' . $parameter->id, $parameter->_name, $this->eshop->parameters->get_product_parameter($this->input->post('product_id'), $parameter->id));
                }
            }
        }

        $data['field_id'] = $this->admin->form->get_field_id(TRUE);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
        $this->admin->form->field_id_prefix = '';
    }
    
    function ajax_product_type()
    {
        if(!$this->input->is_ajax_request()) show_404();

        $data = array();
        
        $this->admin->form->field_id_prefix = 'product_type_';
        
        $product_type_id = $this->input->post('product_type_id');
        $this->_validation_product_type_variables($product_type_id);
        if(form_sent()) $this->admin->form->validate();
        
        if(strlen($product_type_id) == 0)
        {
            $data['result'] = TRUE;
            $data['content'] = '';
        }
        else
        {
            $data['result'] = $this->e_product_types_model->item_exists($product_type_id);
            $data['content'] = '';
            $data['error'] = __('error_11');

            if($data['result'])
            {
                foreach($this->eshop->product_types->get_variables($product_type_id) as $product_type_variable)
                {
                    $data['content'] .= $this->admin->form->get_field_row('select', 'product_type_variable_' . $product_type_variable['id'], $product_type_variable['name'], $product_type_variable['values'], $this->eshop->product_types->get_product_variable_value($this->input->post('product_id'), $product_type_variable['id']), TRUE);
                }
            }
        }
        
        $data['field_id'] = $this->admin->form->get_field_id(TRUE);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
        $this->admin->form->field_id_prefix = '';
    }
    
    function ajax_variants()
    {
        if(!$this->input->is_ajax_request()) show_404();
        
        $data = array();
        
        $this->admin->form->field_id_prefix = 'variant_';
        
        $variant_ids = $this->input->post('variant_ids');
        $product_id = $this->input->post('product_id');
        
        $this->_validation_variants($product_id);
        if(form_sent()) $this->admin->form->validate();
        
        $data['result'] = TRUE;
        $data['content'] = '';
        
        if(is_array($variant_ids))
        {
            if(count($variant_ids) != count(array_intersect($variant_ids, $this->eshop->variants->get_available_variant_ids()))) $data['result'] = FALSE;

            if($data['result'])
            {
                $variants = array();
                
                foreach($variant_ids as $variant_id)
                {
                    $options = array();
                    
                    $this->e_variant_values_model->where('variant_id', '=', $variant_id);
                    foreach($this->e_variant_values_model->get_ids() as $variant_value_id)
                    {
                        $options[$variant_value_id] = $this->e_variant_values_model->$variant_value_id->_name;
                    }
                    
                    $variant_data = $this->e_variants_model->get_item($variant_id);
                    $variants[$variant_id] = array(
                        'name' => $variant_data->_name,
                        'options' => $options
                    );
                }

                $variant_selector = '';
                
                foreach($variants as $variant_id => $variant_data)
                {
                    $options = array('' => '') + $variant_data['options'];
                    $variant_selector .= form_dropdown('variant_ids_selected_' . $variant_id, $options, $this->input->post('variant_ids_selected_' . $variant_id), 'id="' . $this->admin->form->get_new_field_id() . '" data-page="chosen" class="variant_select chosen" data-placeholder="' . $variant_data['name'] . '"');
                }

                $field_wrap_data = array();

                $field_wrap_data['error'] = '';
                $field_wrap_data['info'] = '';
                $field_wrap_data['multilingual'] = FALSE;
                $field_wrap_data['field_id'] = $this->admin->form->get_new_field_id();
                $field_wrap_data['title'] = __('field_25');
                $field_wrap_data['field'] = $variant_selector;
                $field_wrap_data['class'] = '';
                $field_wrap_data['label'] = '';
                $field_wrap_data['rules'] = '';

                $data['content'] .= $this->admin->load_view('fields/general/field_wrap', $field_wrap_data, TRUE);
                
                $product_parameter_group_id = $this->input->post('product_parameter_group_id');
                
                if(strlen($product_parameter_group_id) > 0)
                {
                    if($this->e_product_parameter_groups_model->item_exists($product_parameter_group_id))
                    {
                        $this->e_product_parameters_model->where('product_parameter_group_id', '=', $product_parameter_group_id);
                        $parameters = $this->e_product_parameters_model->get_data();
                    }
                }
                
                foreach($this->eshop->variants->get_variant_combinations($variant_ids) as $combination)
                {
                    $variant = $this->eshop->variants->array2string($combination);
                    
                    $this->admin->form->set_field_class('variant_field variant_field_' . $variant, TRUE);
                    $variant_price = $this->eshop->variants->get_product_variant_data($product_id, $variant, '_price');
                    $data['content'] .= $this->admin->form->get_field_row('input', '_price_variant_' . $variant, __('field_3'), (strlen($variant_price) > 0) ? doubleval($variant_price) : '');
                    $data['content'] .= $this->admin->form->get_field_row('input', 'quantity_variant_' . $variant, __('field_4'), $this->eshop->variants->get_product_variant_data($product_id, $variant, 'quantity'));
                    $data['content'] .= $this->admin->form->get_field_row('input', 'ean_variant_' . $variant, __('field_8'), $this->eshop->variants->get_product_variant_data($product_id, $variant, 'ean'));
                    $data['content'] .= $this->admin->form->get_field_row('input', 'sku_variant_' . $variant, __('field_7'), $this->eshop->variants->get_product_variant_data($product_id, $variant, 'sku'));
                    $data['content'] .= $this->admin->form->get_field_row('imagepicker', 'image_variant_' . $variant, __('field_27'), $this->eshop->variants->get_product_variant_data($product_id, $variant, 'image'), TRUE);
                    $data['content'] .= $this->admin->form->get_field_row('product_gallery', 'product_gallery_id_variant_' . $variant, __('field_26'), $this->eshop->variants->get_product_variant_data($product_id, $variant, 'product_gallery_id'), TRUE);
                    
                    // Parameters
                    if(strlen($product_parameter_group_id) > 0)
                    foreach($parameters as $parameter)
                    {
                        $data['content'] .= $this->admin->form->get_field_row('input', '_variant_parameter_' . $parameter->id . '_' . $variant, $parameter->_name, $this->eshop->parameters->get_product_variant_parameter($product_id, $variant, $parameter->id));
                    }
                }
                
                $data['content'] .= $this->admin->load_view('variants/start_script', array(), TRUE);
            }
            else
            {
                $data['error'] = __('error_12');
            }
        }
        
        $data['field_id'] = $this->admin->form->get_field_id(TRUE);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
        $this->admin->form->field_id_prefix = '';
    }
    
    function delete($product_id = '')
    {
        // TODO: nevymazavat ale presuvat do kosa (spravit novu premennu v tabulke cms_eshop_products s nazvom "active" alebo "deleted" alebo "in_trash" to je jedno)
        // TODO: domysletako adminovat produktyktore su v kosi (ak je v kosi a nie je v ziadnej objednavke uz nikdy ho nebudem pravdepodobne potrebovat)
        // TODO: aj objednavky "davat do kosa" ??? ...
        
        if($this->e_products_model->item_exists($product_id))
        {
            $this->e_products_model->delete_item($product_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation()
    {
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('_alias', __('field_2'), 'trim|url_title|max_length[255]|valid_alias');
        $this->admin->form->set_rules('_price', __('field_3'), 'trim|price|plus|required');
        $this->admin->form->set_rules('quantity', __('field_4'), 'trim|is_natural|required|max_length[255]');
        $this->admin->form->set_rules('_description', __('field_5'), 'trim');
        $this->admin->form->set_rules('public', __('field_6'), 'trim|intval');
        $this->admin->form->set_rules('sku', __('field_7'), 'trim|max_length[50]');
        $this->admin->form->set_rules('ean', __('field_8'), 'trim|max_length[50]');
        $this->admin->form->set_rules('tax_id', __('field_9'), 'trim|required|item_exists_eshop[taxes]');
        $this->admin->form->set_rules('manufacturer_id', __('field_10'), 'trim|item_exists_eshop[manufacturers]');
        $this->admin->form->set_rules('distributor_id', __('field_11'), 'trim|item_exists_eshop[distributors]');
        $this->admin->form->set_rules('_meta_title', __('field_12'), 'trim|max_length[255]');
        $this->admin->form->set_rules('_meta_description', __('field_13'), 'trim');
        $this->admin->form->set_rules('_meta_keywords', __('field_14'), 'trim');
        $this->admin->form->set_rules('changefreq', __('field_15'), 'trim|required|changefreq|max_length[255]');
        $this->admin->form->set_rules('index', __('field_16'), 'trim|intval');
        $this->admin->form->set_rules('sitemap_priority', __('field_17'), 'trim|required|sitemap_priority');
        $this->admin->form->set_rules('tpl', __('field_18'), 'trim|max_length[255]|tpl[products]');
        $this->admin->form->set_rules('product_parameter_group_id', __('field_20'), 'trim|item_exists_eshop[product_parameter_groups]');
        $this->admin->form->set_rules('product_type_id', __('field_21'), 'trim|item_exists_eshop[product_types]');
        $this->admin->form->set_rules('image', __('field_27'), 'trim');
        $this->admin->form->set_rules('product_gallery_id', __('field_26'), 'trim|item_exists_eshop[product_galleries]');
    }
    
    protected function _validation_product_type_variables($product_type_id = '')
    {
        foreach($this->eshop->product_types->get_variable_ids($product_type_id) as $product_type_variable_id)
        {
            $this->admin->form->set_rules('product_type_variable_' . $product_type_variable_id, $this->e_product_type_variables_model->$product_type_variable_id->name, 'trim|required|product_type_variable_value[' . $product_type_variable_id . ']');
        }
    }
    
    protected function _validation_variants($product_id = '')
    {
        foreach($this->eshop->variants->get_variant_combinations((array)$this->input->post('variant_ids')) as $variant)
        {
            $variant = $this->eshop->variants->array2string($variant);
            if($variant == '') continue;
            $this->admin->form->set_rules('_price_variant_' . $variant, __('field_3'), 'trim|price|plus|required');
            $this->admin->form->set_rules('product_gallery_id_variant_' . $variant, __('field_26'), 'trim|item_exists_eshop[product_galleries]');
        }
    }
    
}