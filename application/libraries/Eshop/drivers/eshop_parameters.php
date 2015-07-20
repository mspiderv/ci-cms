<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop_parameters extends CI_Driver {

    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('product_parameters');
        $this->CI->cms->model->load_eshop('product_parameter_data');
        $this->CI->cms->model->load_eshop('product_parameter_groups');
        $this->CI->cms->model->load_eshop('product_variant_parameters');
    }
    
    /* Parametre */
    
    function set_product_parameter($product_id = '', $parameter_id = '', $value = '', $lang = NULL)
    {
        if($lang == NULL) $lang = lang();
        
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->CI->e_product_parameters_model->item_exists($parameter_id)) return FALSE;
        
        if($this->product_has_parameter_in_db($product_id, $parameter_id))
        {
            if(strlen($value) == 0)
            {
                $this->delete_product_parameter($product_id, $parameter_id);
            }
            else
            {
                $this->CI->e_product_parameter_data_model->set_item_data($this->get_product_parameter_id($product_id, $parameter_id), array($lang . '_value' => $value));
            }
        }
        elseif(strlen($value) > 0)
        {
            $data = array();
            
            $data['product_id'] = $product_id;
            $data['parameter_id'] = $parameter_id;
            $data['_value'] = $value;
            
            $this->CI->e_product_parameter_data_model->add_item($data);
        }
        
        return TRUE;
    }
    
    function get_product_parameter($product_id = '', $parameter_id = '', $lang = NULL)
    {
        if($lang == NULL) $lang = lang();
        
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->CI->e_product_parameters_model->item_exists($parameter_id)) return FALSE;
        
        $this->CI->e_product_parameter_data_model->where('product_id', '=', $product_id);
        $this->CI->e_product_parameter_data_model->where('parameter_id', '=', $parameter_id);
        
        $parameter_data = $this->CI->e_product_parameter_data_model->get_ids();
        if(count($parameter_data) == 0) return FALSE;
        $parameter_data = $parameter_data[0];
        $parameter_data = $this->CI->e_product_parameter_data_model->get_item($parameter_data);
        
        $parameter = lang() . '_value';
        return $parameter_data->$parameter;
    }
    
    function delete_product_parameter($product_id = '', $parameter_id = '')
    {
        if($this->product_has_parameter_in_db($product_id, $parameter_id))
        {
            $this->CI->e_product_parameter_data_model->delete_item($this->get_product_parameter_id($product_id, $parameter_id));
        }
        
        return TRUE;
    }
    
    function delete_product_parameters($product_id = '')
    {
        $this->CI->e_product_parameter_data_model->where('product_id', '=', $product_id);
        $this->CI->e_product_parameter_data_model->delete();
        return TRUE;
    }
    
    function product_has_parameter_in_db($product_id = '', $parameter_id = '')
    {
        return ($this->get_product_parameter_id($product_id, $parameter_id) !== FALSE);
    }
    
    function product_has_parameter($product_id = '', $parameter_id = '')
    {
        return (in_array($parameter_id, $this->get_parameter_ids_in_group($this->CI->e_products_model->$product_id->product_parameter_group_id)));
    }
    
    function get_product_parameter_id($product_id = '', $parameter_id = '')
    {
        $this->CI->e_product_parameter_data_model->where('product_id', '=', $product_id);
        $this->CI->e_product_parameter_data_model->where('parameter_id', '=', $parameter_id);
        return $this->CI->e_product_parameter_data_model->get_first_id();
    }
    
    function get_parameter_ids_in_group($product_parameter_group_id = '')
    {
        $this->CI->e_product_parameters_model->where('product_parameter_group_id', '=', $product_parameter_group_id);
        return $this->CI->e_product_parameters_model->get_ids();
    }
    
    /* Parametre variánt */
    
    function set_product_variant_parameter($product_id = '', $variant = '', $parameter_id = '', $value = '', $lang = NULL)
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->product_has_parameter($product_id, $parameter_id)) return FALSE;
        if(!$this->CI->eshop->variants->is_valid_product_variant($product_id, $variant)) return FALSE;
        
        if($lang == NULL) $lang = lang();
        
        if($this->product_variant_has_parameter($product_id, $variant, $parameter_id))
        {
            if(strlen($value) == 0)
            {
                $this->delete_product_variant_parameter($product_id, $variant, $parameter_id);
            }
            else
            {
                $product_variant_parameter_id = $this->get_product_variant_parameter_id($product_id, $variant, $parameter_id);
                $this->CI->e_product_variant_parameters_model->set_item_data($product_variant_parameter_id, array($lang . '_value' => $value));
            }
        }
        elseif(strlen($value) > 0)
        {
            $data = array();
            
            $data['product_id'] = $product_id;
            $data['variant'] = $variant;
            $data['parameter_id'] = $parameter_id;
            $data[$lang . '_value'] = $value;
            
            $this->CI->e_product_variant_parameters_model->add_item($data);
        }
        
        return TRUE;
    }
    
    function get_product_variant_parameter($product_id = '', $variant = '', $parameter_id = '', $lang = NULL)
    {
        if($this->product_variant_has_parameter($product_id, $variant, $parameter_id))
        {
            if($lang == NULL) $lang = lang();
            $product_variant_parameter_id = $this->get_product_variant_parameter_id($product_id, $variant, $parameter_id);
            return $this->CI->e_product_variant_parameters_model->get_item_data($product_variant_parameter_id, $lang . '_value');
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_product_variant_parameter($product_id = '', $variant = '', $parameter_id = '')
    {
        if($this->product_variant_has_parameter($product_id, $variant, $parameter_id))
        {
            $product_variant_parameter_id = $this->get_product_variant_parameter_id($product_id, $variant, $parameter_id);
            $this->CI->e_product_variant_parameters_model->delete_item($product_variant_parameter_id);
        }
        
        return TRUE;
    }
    
    function delete_product_variant_parameters($product_id = '', $variant = '')
    {
        $this->CI->e_product_variant_parameters_model->where('product_id', '=', $product_id);
        $this->CI->e_product_variant_parameters_model->where('variant', '=', $variant);
        $this->CI->e_product_variant_parameters_model->delete();
        return TRUE;
    }
    
    function get_product_variant_parameter_id($product_id = '', $variant = '', $parameter_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->product_has_parameter($product_id, $parameter_id)) return FALSE;
        if(!$this->CI->eshop->variants->is_valid_product_variant($product_id, $variant)) return FALSE;
        
        $this->CI->e_product_variant_parameters_model->where('product_id', '=', $product_id);
        $this->CI->e_product_variant_parameters_model->where('variant', '=', $variant);
        $this->CI->e_product_variant_parameters_model->where('parameter_id', '=', $parameter_id);
        return $this->CI->e_product_variant_parameters_model->get_first_id();
    }
    
    function product_variant_has_parameter($product_id = '', $variant = '', $parameter_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->product_has_parameter($product_id, $parameter_id)) return FALSE;
        if(!$this->CI->eshop->variants->is_valid_product_variant($product_id, $variant)) return FALSE;
        
        return (intval($this->get_product_variant_parameter_id($product_id, $variant, $parameter_id)) > 0);
    }
    
}