<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse_category extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->load->driver('eshop');
        
        $this->CI->cms->model->load_eshop('categories');
    }
    
    function category_exists($category_id = '')
    {
        return ($this->CI->e_categories_model->item_exists($category_id));
    }
    
    function get_category_url($category_id = '', $lang = '')
    {
        if(!$this->category_exists($category_id)) return FALSE;
        
        return $this->get_category_segments($category_id, TRUE, $lang) . '/' . $this->CI->parse->url->create_id_segment('category', $category_id);
    }
    
    function get_category_segments($category_id = '', $string = FALSE, $lang = '')
    {
        if($this->category_exists($category_id))
        {
            $alias_var = $lang . '_alias';
            $segments = array();
            
            // Aliasy nadradených kategórií
            if(!db_config_bool('category_only_one_alias'))
            {
                foreach(array_reverse($this->CI->eshop->categories->get_category_parents($category_id)) as $parent_category_id)
                {
                    $segments[] = $this->CI->e_categories_model->$parent_category_id->$alias_var;
                }
            }
            
            // Alias konkrétnej kategórie
            $alias = $this->CI->e_categories_model->$category_id->$alias_var;
            if(strlen($alias) > 0) $segments[] = $alias;
            
            return ($string) ? implode('/', $segments) : $segments;
        }
        else
        {
            return ($string) ? '' : array();
        }
    }
    
    function check_category_segments($category_id = '', $segments = NULL)
    {
        if(!$this->category_exists($category_id)) return FALSE;
        
        if($segments == NULL) $segments = $this->CI->uri->segment_array();
        
        $real_segments = $this->get_category_segments($category_id);
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
    
    function get_category_tpl($category_id = '')
    {
        if(!$this->category_exists($category_id)) return '';
        
        $category_tpl = $this->CI->e_categories_model->$category_id->tpl;
        if(strlen($category_tpl) > 0) return $category_tpl;
        
        return db_config('default_category_tpl');
    }
    
    function get_category_data($category_id = '')
    {
        $data = (array)$this->CI->e_categories_model->get_item($category_id);
        
        $data['products'] = $this->get_category_products($category_id);
        
        return $data;
    }
    
    function get_category_products($category_id = '')
    {
        if(!$this->CI->e_categories_model->item_exists($category_id)) return array();
        
        // TODO: ako budu produkty zoradene ?
        // TODO: strankovanie
        
        $products = array();
        
        $this->CI->cms->model->load_eshop('products_in_categories');
        $this->CI->cms->model->load_eshop('products');
        
        $this->CI->e_products_in_categories_model->where('category_id', '=', $category_id);
        
        foreach($this->CI->e_products_in_categories_model->get_data_in_col('product_id') as $product_id)
        {
            $product_data = $this->CI->e_products_model->get_item($product_id);
            
            if($product_data->public > 0 && $product_data->quantity > 0)
            {
                $products[] = $product_data;
            }
        }
        
        return $products;
    }
    
    function show($category_id = '')
    {
        if(!$this->category_exists($category_id)) return FALSE;
        
        // Add resources
        $this->CI->parse->add_resources('product_category', $category_id);
        
        $category_data = (array)$this->get_category_data($category_id);
        
        $category_tpl = cfg('folder', 'categories') . '/' . $this->get_category_tpl($category_id);
        
        $category_data[cfg('variable', 'lang')] = $this->CI->parse->lang->get_lang($category_tpl);
        
        $this->load_view($category_tpl, $category_data);
    }
    
}