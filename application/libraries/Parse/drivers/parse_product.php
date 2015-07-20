<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse_product extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('products');
        $this->CI->load->driver('eshop');
    }
    
    function product_exists($product_id = '')
    {
        return ($this->CI->e_products_model->item_exists($product_id));
    }
    
    function get_product_url($product_id = '', $lang = '')
    {
        if(!$this->product_exists($product_id)) return FALSE;
        
        return $this->get_product_segments($product_id, TRUE, $lang) . '/' . $this->CI->parse->url->create_id_segment('product', $product_id);
    }
    
    function get_product_segments($product_id = '', $string = FALSE, $lang = '')
    {
        if($this->product_exists($product_id))
        {
            $alias_var = $lang . '_alias';
            $segments = array();
            
            // Nejaké iné segmenty
            if(!db_config_bool('product_only_one_alias'))
            {
                // $segments[] = '';
            }
            
            // Alias konkrétneho produktu
            $alias = $this->CI->e_products_model->$product_id->$alias_var;
            if(strlen($alias) > 0) $segments[] = $alias;
            
            return ($string) ? implode('/', $segments) : $segments;
        }
        else
        {
            return ($string) ? '' : array();
        }
    }
    
    function check_product_segments($product_id = '', $segments = NULL)
    {
        if(!$this->product_exists($product_id)) return FALSE;
        
        if($segments == NULL) $segments = $this->CI->uri->segment_array();
        
        $real_segments = $this->get_product_segments($product_id);
        $real_segments_count = count($real_segments);
        
        if(implode('/', $real_segments) == implode('/', array_slice($segments, 0, $real_segments_count)))
        {
            return array_slice($segments, $real_segments_count + 1);
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_product_tpl($product_id = '')
    {
        if(!$this->product_exists($product_id)) return '';
        
        $product_tpl = $this->CI->e_products_model->$product_id->tpl;
        if(strlen($product_tpl) > 0) return $product_tpl;
        
        return db_config('default_product_tpl');
    }
    
    function get_product_data($product_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return array();
        
        // Product data
        $data = (array)$this->CI->e_products_model->get_item($product_id);
        
        // Tax
        $data['tax'] = $this->get_product_tax($product_id);
        
        // Manufacturer
        $data['manufacturer'] = $this->get_product_manufacturer($product_id);
        
        // Distributor
        $data['distributor'] = $this->get_product_distributor($product_id);
        
        // Product parameter group
        $data['product_parameter_group'] = $this->get_product_parameter_group($product_id);
        
        // Product type data
        $data['product_type_data'] = $this->get_product_type_data($product_id);
        
        // Product gallery
        $data['product_gallery'] = $this->get_product_gallery($product_id);
        
        //my_print($data);die();
        
        return $data;
    }
    
    function get_product_tax($product_id = '')
    {
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('taxes');
        
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        return $this->CI->e_taxes_model->get_item($this->CI->e_products_model->get_item_data($product_id, 'tax_id'));
    }
    
    function get_product_manufacturer($product_id = '')
    {
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('manufacturers');
        
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        $manufacturer_id = $this->CI->e_products_model->get_item_data($product_id, 'manufacturer_id');
        if(!$this->CI->e_manufacturers_model->item_exists($manufacturer_id)) return FALSE;
        return $this->CI->e_manufacturers_model->get_item($manufacturer_id);
    }
    
    function get_product_distributor($product_id = '')
    {
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('distributors');
        
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        $distributor_id = $this->CI->e_products_model->get_item_data($product_id, 'distributor_id');
        if(!$this->CI->e_distributors_model->item_exists($distributor_id)) return FALSE;
        return $this->CI->e_distributors_model->get_item($distributor_id);
    }
    
    function get_product_parameter_group($product_id = '')
    {
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('product_parameter_groups');
        
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        
        $product_parameter_group_id = $this->CI->e_products_model->get_item_data($product_id, 'product_parameter_group_id');
        if(!$this->CI->e_product_parameter_groups_model->item_exists($product_parameter_group_id)) return FALSE;
        
        $product_parameter_group = $this->CI->e_product_parameter_groups_model->get_item($product_parameter_group_id);
        
        $this->CI->cms->model->load_eshop('product_parameters');
        $this->CI->e_product_parameters_model->where('product_parameter_group_id', '=', $product_parameter_group_id);
        $product_parameter_group->data = array();
        
        foreach($this->CI->e_product_parameters_model->get_data_in_col('_name') as $parameter_id => $parameter_name)
        {
            $parameter = new stdClass();
            
            $parameter->id = $parameter_id;
            $parameter->name = $parameter_name;
            $parameter->value = $this->CI->eshop->parameters->get_product_parameter($product_id, $parameter->id);
            
            $product_parameter_group->data[] = $parameter;
        }
        
        return $product_parameter_group;
    }
    
    function get_product_type_data($product_id = '')
    {
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('product_types');
        
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        
        $product_type_id = $this->CI->e_products_model->get_item_data($product_id, 'product_type_id');
        if(!$this->CI->e_product_types_model->item_exists($product_type_id)) return FALSE;
        
        $product_type_data = $this->CI->e_product_types_model->get_item($product_type_id);
        
        $this->CI->cms->model->load_eshop('product_type_variables');
        $this->CI->cms->model->load_eshop('product_type_variable_values');
        $product_type_data->data = array();
        
        foreach($this->CI->eshop->product_types->get_variable_ids($product_type_id) as $product_type_variable_id)
        {
            $product_type_variable = new stdClass();
            
            $product_type_variable->name = $this->CI->e_product_type_variables_model->get_item_data($product_type_variable_id, 'name');
            $product_type_variable->value_id = $this->CI->eshop->product_types->get_product_variable_value($product_id, $product_type_variable_id);
            $product_type_variable->value = $this->CI->e_product_type_variable_values_model->get_item_data($product_type_variable->value_id, '_name');
            
            $product_type_data->data[] = $product_type_variable;
        }
        
        return $product_type_data;
    }
    
    function get_product_gallery($product_id = '')
    {
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('product_galleries');
        
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        
        $product_gallery_id = $this->CI->e_products_model->get_item_data($product_id, 'product_gallery_id');
        if(!$this->CI->e_product_galleries_model->item_exists($product_gallery_id)) return FALSE;
        
        $product_gallery = $this->CI->e_product_galleries_model->get_item($product_gallery_id);
        
        // Images
        $this->CI->cms->model->load_eshop('product_gallery_images');
        
        $this->CI->e_product_gallery_images_model->where('product_gallery_id', '=', $product_gallery_id);
        $product_gallery->images = $this->CI->e_product_gallery_images_model->get_data();
        
        return $product_gallery;
    }
    
    function show($product_id = '')
    {
        if(!$this->product_exists($product_id)) return FALSE;
        
        $product_data = (array)$this->get_product_data($product_id);
        
        // Add resources
        $this->CI->parse->add_resources('product', $product_id);
        
        foreach($this->CI->eshop->products->get_product_categories($product_id) as $product_category_id)
        {
            $this->CI->parse->add_resources('product_category', $product_category_id);
        }
        
        $product_tpl = cfg('folder', 'products') . '/' . $this->get_product_tpl($product_id);
        
        $product_data[cfg('variable', 'lang')] = $this->CI->parse->lang->get_lang($product_tpl);
        
        $this->load_view($product_tpl, $product_data);
    }
    
}