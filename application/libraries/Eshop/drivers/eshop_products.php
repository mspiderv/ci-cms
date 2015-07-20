<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop_products extends CI_Driver {

    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('products');
        $this->CI->cms->model->load_eshop('categories');
        $this->CI->cms->model->load_eshop('products_in_categories');
        $this->CI->cms->model->load_eshop('signs');
        $this->CI->cms->model->load_eshop('products_signs');
        $this->CI->cms->model->load_eshop('relevant_products');
    }
    
    function add_product_to_category($product_id = '', $category_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            if($this->CI->e_categories_model->item_exists($category_id))
            {
                $this->CI->e_products_in_categories_model->where('product_id', '=', $product_id);

                if(in_array($category_id, $this->CI->e_products_in_categories_model->get_data_in_col('category_id'))) return TRUE;

                $new_product_in_category = array();

                $new_product_in_category['product_id'] = $product_id;
                $new_product_in_category['category_id'] = $category_id;

                return $this->CI->e_products_in_categories_model->add_item($new_product_in_category);
            }
            else
            {
                show_error('Produkt s ID <strong>' . $product_id . '</strong> sa nepodarilo pridať do kategórie s ID <strong>' . $category_id . '</strong>, pretože táto kategória neexistuje.');
            }
        }
        else
        {
            show_error('Produkt s ID <strong>' . $product_id . '</strong> sa nepodarilo pridať do kategórie s ID <strong>' . $category_id . '</strong>, pretože tento produkt neexistuje.');
        }
    }
    
    function delete_product_from_category($product_id = '', $category_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id) && $this->CI->e_categories_model->item_exists($category_id))
        {
            $this->CI->e_products_in_categories_model->where('product_id', '=', $product_id);
            $this->CI->e_products_in_categories_model->where('category_id', '=', $category_id);
            $this->CI->e_products_in_categories_model->delete();
            
            return TRUE;
        }
        else
        {
            return TRUE;
        }
    }
    
    function get_product_categories($product_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $this->CI->e_products_in_categories_model->where('product_id', '=', $product_id);
            return $this->CI->e_products_in_categories_model->get_data_in_col('category_id');
        }
        else
        {
            return array();
        }
    }
    
    function set_product_categories($product_id = '', $product_category_ids = array())
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $already_in = $this->get_product_categories($product_id);
            
            // Add to categories
            foreach(array_diff($product_category_ids, $already_in) as $add_to_category_id)
            {
                $this->add_product_to_category($product_id, $add_to_category_id);
            }
            
            // Delete from categories
            foreach(array_diff($already_in, $product_category_ids) as $delete_from_category_id)
            {
                $this->delete_product_from_category($product_id, $delete_from_category_id);
            }

            return TRUE;
        }
        else
        {
            return array();
        }
    }
    
    // Príznaky
    
    function add_product_sign($product_id = '', $sign_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id) && $this->CI->e_signs_model->item_exists($sign_id))
        {
            if(!$this->product_has_sign($product_id, $sign_id))
            {
                $data = array();
                
                $data['product_id'] = $product_id;
                $data['sign_id'] = $sign_id;
                
                $this->CI->e_products_signs_model->add_item($data);
            }
            
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_product_sign($product_id = '', $sign_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id) && $this->CI->e_signs_model->item_exists($sign_id))
        {
            if($this->product_has_sign($product_id, $sign_id))
            {
                $this->CI->e_products_signs_model->where('product_id', '=', $product_id);
                $this->CI->e_products_signs_model->where('sign_id', '=', $sign_id);
                $this->CI->e_products_signs_model->delete();
            }
            
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_product_sign_ids($product_id = '', $sign_ids = array())
    {
        $status = TRUE;
        
        foreach($sign_ids as $sign_id)
        {
            if(!$this->delete_product_sign($product_id, $sign_id)) $status = FALSE;
        }
        
        return $status;
    }
    
    function delete_all_product_signs($product_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $this->CI->e_products_signs_model->where('product_id', '=', $product_id);
            $this->CI->e_products_signs_model->delete();
        }
        else
        {
            return FALSE;
        }
    }
    
    function set_product_signs($product_id = '', $sign_ids = array())
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $status = TRUE;
            
            $already_has = $this->get_product_sign_ids($product_id);
            
            // Add signs
            foreach((array)@array_diff((array)$sign_ids, $already_has) as $add_sign_id)
            {
                if((int)$add_sign_id == 0) continue;
                if(!$this->add_product_sign($product_id, $add_sign_id)) $status = FALSE;
            }
            
            // Delete signs
            foreach((array)@array_diff($already_has, (array)$sign_ids) as $delete_sign_id)
            {
                if((int)$delete_sign_id == 0) continue;
                if(!$this->delete_product_sign($product_id, $delete_sign_id)) $status = FALSE;
            }

            return $status;
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_product_sign_ids($product_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $this->CI->e_products_signs_model->where('product_id', '=', $product_id);
            return $this->CI->e_products_signs_model->get_data_in_col('sign_id');
        }
        else
        {
            return array();
        }
    }
    
    function product_has_sign($product_id = '', $sign_id = '')
    {
        return in_array($sign_id, $this->get_product_sign_ids($product_id));
    }
    
    function get_signs_select_data()
    {
        return $this->CI->e_signs_model->get_data_in_col('_name');
    }
    
    // Súvisiaci tovar
    
    function add_relevant_product($product_id = '', $relevant_product_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id) && $this->CI->e_products_model->item_exists($relevant_product_id))
        {
            if(!$this->product_has_relevant_product($product_id, $relevant_product_id))
            {
                $data = array();
                
                $data['product_id'] = $product_id;
                $data['relevant_product_id'] = $relevant_product_id;
                
                $this->CI->e_relevant_products_model->add_item($data);
            }
            
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_relevant_product($product_id = '', $relevant_product_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id) && $this->CI->e_products_model->item_exists($relevant_product_id))
        {
            if($this->product_has_relevant_product($product_id, $relevant_product_id))
            {
                $this->CI->e_relevant_products_model->where('product_id', '=', $product_id);
                $this->CI->e_relevant_products_model->where('relevant_product_id', '=', $relevant_product_id);
                $this->CI->e_relevant_products_model->delete();
            }
            
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_relevant_product_ids($product_id = '', $relevant_product_ids = array())
    {
        $status = TRUE;
        
        foreach($relevant_product_ids as $relevant_product_id)
        {
            if(!$this->delete_relevant_product($product_id, $relevant_product_id)) $status = FALSE;
        }
        
        return $status;
    }
    
    function delete_all_relevant_products($product_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $this->CI->e_relevant_products_model->where('product_id', '=', $product_id);
            $this->CI->e_relevant_products_model->delete();
        }
        else
        {
            return FALSE;
        }
    }
    
    function set_relevant_products($product_id = '', $relevant_product_ids = array())
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $status = TRUE;
            
            $already_has = $this->get_relevant_product_ids($product_id);
            
            // Add relevant_products
            foreach((array)@array_diff((array)$relevant_product_ids, $already_has) as $add_relevant_product_id)
            {
                if((int)$add_relevant_product_id == 0) continue;
                if(!$this->add_relevant_product($product_id, $add_relevant_product_id)) $status = FALSE;
            }
            
            // Delete relevant_products
            foreach((array)@array_diff($already_has, (array)$relevant_product_ids) as $delete_relevant_product_id)
            {
                if((int)$delete_relevant_product_id == 0) continue;
                if(!$this->delete_relevant_product($product_id, $delete_relevant_product_id)) $status = FALSE;
            }

            return $status;
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_relevant_product_ids($product_id = '')
    {
        if($this->CI->e_products_model->item_exists($product_id))
        {
            $this->CI->e_relevant_products_model->where('product_id', '=', $product_id);
            return $this->CI->e_relevant_products_model->get_data_in_col('relevant_product_id');
        }
        else
        {
            return array();
        }
    }
    
    function product_has_relevant_product($product_id = '', $relevant_product_id = '')
    {
        return in_array($relevant_product_id, $this->get_relevant_product_ids($product_id));
    }
    
    function get_relevant_products_select_data($product_id = '')
    {
        $select_data = $this->CI->e_products_model->get_data_in_col('_name');
        if(intval($product_id) > 0) unset($select_data[$product_id]);
        return $select_data;
    }
    
}