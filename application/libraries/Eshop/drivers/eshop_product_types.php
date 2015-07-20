<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop_product_types extends CI_Driver {

    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('product_types');
        $this->CI->cms->model->load_eshop('product_type_variables');
        $this->CI->cms->model->load_eshop('product_type_variable_values');
        $this->CI->cms->model->load_eshop('product_variables');
    }
    
    // Vracia dáta
    
    function get_variable_ids($product_type_id = '')
    {
        $product_type_variable_ids = array();
        
        $this->CI->e_product_type_variables_model->where('product_type_id', '=', $product_type_id);
        foreach($this->CI->e_product_type_variables_model->get_ids() as $product_type_variable_id)
        {
            if(count($this->get_variable_value_ids($product_type_variable_id)) > 0)
            {
                $product_type_variable_ids[] = $product_type_variable_id;
            }
        }
        
        return $product_type_variable_ids;
    }
    
    function get_variable_value_ids($product_type_variable_id = '')
    {
        $this->CI->e_product_type_variable_values_model->where('product_type_variable_id', '=', $product_type_variable_id);
        return $this->CI->e_product_type_variable_values_model->get_ids();
    }
    
    function get_variables($product_type_id = '')
    {
        $data = array();
        
        foreach($this->get_variable_ids($product_type_id) as $product_type_variable_id)
        {
            $data[$product_type_variable_id] = array(
                'id' => $product_type_variable_id,
                'name' => $this->CI->e_product_type_variables_model->$product_type_variable_id->name,
                'values' => $this->get_variable_value_select_data($product_type_variable_id)
            );
        }
        
        return $data;
    }
    
    function get_variable_value_select_data($product_type_variable_id = '')
    {
        $this->CI->e_product_type_variable_values_model->where('product_type_variable_id', '=', $product_type_variable_id);
        return $this->CI->e_product_type_variable_values_model->get_data_in_col('_name');
    }
    
    function get_product_type_ids()
    {
        $product_type_ids = array();
        
        foreach($this->CI->e_product_types_model->get_ids() as $product_type_id)
        {
            if(count($this->get_variable_ids($product_type_id)) > 0)
            {
                $product_type_ids[] = $product_type_id;
            }
        }
        
        return $product_type_ids;
    }
    
    function get_product_types_select_data()
    {
        $select_data = array();
        
        foreach($this->get_product_type_ids() as $product_type_id)
        {
            $select_data[$product_type_id] = $this->CI->e_product_types_model->$product_type_id->name;
        }
        
        return $select_data;
    }
    
    // Upravuje premenné produktov
    
    function set_product_variable_value($product_id = '', $product_type_variable_id = '', $product_type_variable_value_id = '')
    {
        if(!$this->CI->e_products_model->item_exists($product_id)) return FALSE;
        if(!$this->CI->e_product_type_variables_model->item_exists($product_type_variable_id)) return FALSE;
        if(!in_array($product_type_variable_value_id, $this->get_variable_value_ids($product_type_variable_id))) return FALSE;
        
        if($this->product_has_variable_value($product_id, $product_type_variable_id))
        {
            $this->CI->e_product_variables_model->set_item_data($this->get_product_variable_id($product_id, $product_type_variable_id), array('product_type_variable_value_id' => $product_type_variable_value_id));
        }
        else
        {
            $data = array();
            
            $data['product_id'] = $product_id;
            $data['product_type_variable_id'] = $product_type_variable_id;
            $data['product_type_variable_value_id'] = $product_type_variable_value_id;
            
            $this->CI->e_product_variables_model->add_item($data);
        }
        
        return TRUE;
    }
    
    function get_product_variable_value($product_id = '', $product_type_variable_id = '')
    {
        $this->CI->e_product_variables_model->where('product_id', '=', $product_id);
        $this->CI->e_product_variables_model->where('product_type_variable_id', '=', $product_type_variable_id);
        $product_variable_ids = $this->CI->e_product_variables_model->get_ids();
        if(count($product_variable_ids) == 0) return FALSE;
        $product_variable_id = $product_variable_ids[0];
        return $this->CI->e_product_variables_model->$product_variable_id->product_type_variable_value_id;
    }
    
    function get_product_variable_id($product_id = '', $product_type_variable_id = '')
    {
        if($this->product_has_variable_value($product_id, $product_type_variable_id))
        {
            $this->CI->e_product_variables_model->where('product_id', '=', $product_id);
            $this->CI->e_product_variables_model->where('product_type_variable_id', '=', $product_type_variable_id);
            $product_type_variable_value_ids = $this->CI->e_product_variables_model->get_ids();
            return $product_type_variable_value_ids[0];
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_product_variable_values($product_id = '')
    {
        $this->CI->e_product_variables_model->where('product_id', '=', $product_id);
        $this->CI->e_product_variables_model->delete();
        return TRUE;
    }
    
    function delete_product_variable_value($product_id = '', $product_type_variable_id = '')
    {
        if($this->product_has_variable_value($product_id, $product_type_variable_id))
        {
            $this->CI->e_product_variables_model->delete_item($this->get_product_variable_id($product_id, $product_type_variable_id));
        }
        
        return TRUE;
    }
    
    function product_has_variable_value($product_id = '', $product_type_variable_id = '')
    {
        return ($this->get_product_variable_value($product_id, $product_type_variable_id) !== FALSE);
    }
    
}