<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop_variants extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('variants');
        $this->CI->cms->model->load_eshop('variant_values');
        $this->CI->cms->model->load_eshop('product_variants');
        $this->CI->cms->model->load_eshop('product_variant_data');
        //$this->CI->cms->model->load_eshop('product_variant_parameters');
    }
    
    /* Práca s variantami */
    
    function get_available_variant_ids()
    {
        $variant_ids = array();
        
        foreach($this->CI->e_variants_model->get_ids() as $variant_id)
        {
            $this->CI->e_variant_values_model->where('variant_id', '=', $variant_id);
            if(intval($this->CI->e_variant_values_model->get_rows()) > 0)
            {
                $variant_ids[] = $variant_id;
            }
        }
        
        return $variant_ids;
    }
    
    function get_variant_value_ids($variant_id = '')
    {
        $this->CI->e_variant_values_model->where('variant_id', '=', $variant_id);
        return $this->CI->e_variant_values_model->get_ids();
    }
    
    function variant_value_exists($variant_id = '', $variant_value_id = '')
    {
        return (in_array($variant_value_id, $this->get_variant_value_ids($variant_id)));
    }
    
    function get_available_variants_select_data()
    {
        $variants = array();
        
        foreach($this->get_available_variant_ids() as $variant_id)
        {
            $variants[$variant_id] = $this->CI->e_variants_model->$variant_id->_name;
        }
        
        return $variants;
    }
    
    function get_variant_combinations($variant_ids = array())
    {
        $combinations = array();
        $variant_ids = array_intersect((array)$variant_ids, $this->get_available_variant_ids());
        foreach($variant_ids as $variant_id) $combinations[$variant_id] = $this->get_variant_value_ids($variant_id);
        return combinations($combinations, TRUE);
    }
    
    function array2string($variant_array = array())
    {
        $variant_string = '';
        foreach($variant_array as $variant_id => $variant_value_id) $variant_string .= '_' . $variant_id . '-' . $variant_value_id;
        return @substr($variant_string, 1);
    }
    
    function string2array($variant_string = '')
    {
        $variant_array = array();
        foreach((array)explode('_', $variant_string) as $variant)
        {
            @list($variant_id, $variant_value_id) = explode('-', $variant);
            $variant_array[$variant_id] = $variant_value_id;
        }
        return $variant_array;
    }
    
    function variant_exists($variant_id = '')
    {
        return $this->CI->e_variants_model->item_exists($variant_id);
    }
    
    /* Varianty produktov */
    
    function add_product_variant($product_id = '', $variant_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->CI->e_variants_model->item_exists($variant_id)) return FALSE;
        
        if(!$this->product_has_variant($product_id, $variant_id))
        {
            $data = array();
            
            $data['product_id'] = $product_id;
            $data['variant_id'] = $variant_id;
            
            $this->CI->e_product_variants_model->add_item($data);
        }
        
        return TRUE;
    }
    
    function delete_product_variant($product_id = '', $variant_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->CI->e_variants_model->item_exists($variant_id)) return FALSE;
        
        if($this->product_has_variant($product_id, $variant_id))
        {
            $this->CI->e_product_variants_model->delete_item($this->get_product_variant_id($product_id, $variant_id));
        }
        
        return TRUE;
    }
    
    function delete_product_variants($product_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        $this->CI->e_product_variants_model->where('product_id', '=', $product_id);
        $this->CI->e_product_variants_model->delete();
        return TRUE;
    }
    
    function get_product_variants($product_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return array();
        $this->CI->e_product_variants_model->where('product_id', '=', $product_id);
        return $this->CI->e_product_variants_model->get_data_in_col('variant_id');
    }
    
    function get_product_variant_id($product_id = '', $variant_id = '')
    {
        $variants = array_flip($this->get_product_variants($product_id));
        return @$variants[$variant_id];
    }
    
    function product_has_variant($product_id = '', $variant_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->CI->e_variants_model->item_exists($variant_id)) return FALSE;
        return (in_array($variant_id, $this->get_product_variants($product_id)));
    }
    
    function set_product_variants($product_id = '', $variant_ids = array())
    {
        $this->delete_product_variants($product_id);
        
        $status = TRUE;
        
        foreach($variant_ids as $variant_id)
        {
            if(!$this->add_product_variant($product_id, $variant_id))
            {
                $status = FALSE;
            }
        }
        
        return $status;
    }
    
    function is_valid_variant($variant = '')
    {
        foreach($this->string2array($variant) as $variant_id => $variant_value_id)
        {
            if(!$this->variant_exists($variant_id)) return FALSE;
            if(!$this->variant_value_exists($variant_id, $variant_value_id)) return FALSE;
        }
        
        return TRUE;
    }
    
    function is_valid_product_variant($product_id = '', $variant = '')
    {
        if($this->CI->e_products_model->item_exists($product_id) && $this->is_valid_variant($variant))
        {
            $variant_array = $this->string2array($variant);
            
            if(count(array_diff($this->get_product_variants($product_id), array_keys($variant_array))) > 0) return FALSE;
            
            foreach($variant_array as $variant_id => $variant_value_id)
            {
                if(!$this->variant_value_exists($variant_id, $variant_value_id)) return FALSE;
            }
            
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /* Dáta variánt produktov */
    
    function set_product_variant_data($product_id = '', $variant = '', $data = array())
    {
        if($this->CI->e_products_model->item_exists($product_id) && $this->is_valid_product_variant($product_id, $variant))
        {
            $data['product_id'] = $product_id;
            $data['variant'] = $variant;
            $data['quantity'] = intval($data['quantity']);
            
            if($this->product_variant_data_exists($product_id, $variant))
            {
                $this->CI->e_product_variant_data_model->set_item_data($this->get_product_variant_data_id($product_id, $variant), $data);
            }
            else
            {
                $this->CI->e_product_variant_data_model->add_item($data);
            }
            
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_product_variant_data($product_id = '', $variant = '', $variant_var = '')
    {
        if($this->CI->e_products_model->item_exists($product_id) && $this->product_variant_data_exists($product_id, $variant))
        {
            $product_variant_data_id = $this->get_product_variant_data_id($product_id, $variant);
            if($variant_var == '') return $this->CI->e_product_variant_data_model->$product_variant_data_id;
            else return $this->CI->e_product_variant_data_model->$product_variant_data_id->$variant_var;
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_product_variant_data($product_id = '', $variant = '')
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            if($this->product_variant_data_exists($product_id, $variant))
            {
                $this->CI->e_product_variant_data_model->delete_item($this->get_product_variant_data_id($product_id, $variant));
            }
            
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_all_product_variant_data($product_id = '')
    {
        $this->CI->e_product_variant_data_model->where('product_id', '=', $product_id);
        $this->CI->e_product_variant_data_model->delete();
        return TRUE;
    }
    
    function get_product_variant_data_id($product_id = '', $variant = '')
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $this->CI->e_product_variant_data_model->where('product_id', '=', $product_id);
            $this->CI->e_product_variant_data_model->where('variant', '=', $variant);
            return $this->CI->e_product_variant_data_model->get_first_id();
        }
        else
        {
            return FALSE;
        }
    }
    
    function product_variant_data_exists($product_id = '', $variant = '')
    {
        return (intval($this->get_product_variant_data_id($product_id, $variant)) > 0);
    }
    
}