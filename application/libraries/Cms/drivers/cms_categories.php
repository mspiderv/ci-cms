<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_categories extends CI_Driver {
    
    // TODO: mozno dorobit cachovanie niektorych metod napr. get_categories_select_data alebo get_category_parent a pod.
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_system('categories');
    }
    
    function get_categories_select_data($category_id = '')
    {
        $this->CI->load->helper('string');
        $select_data = array('');
        
        foreach($this->get_categories_structure() as $category)
        {
            if(is_array($category) && $this->CI->s_categories_model->item_exists(@$category['id']) && (!$this->CI->s_categories_model->item_exists($category_id) || $this->valid_parent_category_id($category['id'], $category_id)))
            {
                $select_data[$category['id']] = repeater('-&nbsp;', @$category['level']) . $this->CI->s_categories_model->$category['id']->_name;
            }
        }
        
        return $select_data;
    }
    
    function valid_parent_category_id($parent_category_id = '', $category_id = '')
    {
        if(!$this->CI->s_categories_model->item_exists($category_id) || !$this->CI->s_categories_model->item_exists($parent_category_id)) return FALSE;
        if($category_id == $parent_category_id) return FALSE;
        if(in_array($category_id, $this->get_category_parents($parent_category_id))) return FALSE;
        return TRUE;
    }
    
    function get_categories_structure($parent_id = NULL, $level = 0)
    {
        $categories_structure = array();
        
        if((int)$parent_id > 0 && !$this->CI->s_categories_model->item_exists($parent_id)) return array();
        
        $order_cat_col = $this->CI->s_categories_model->get_col('order_cat');
        
        if((int)$parent_id > 0)
        {
            $this->CI->s_categories_model->where($order_cat_col, '=', $parent_id);
        }
        else
        {
            $this->CI->s_categories_model->where($order_cat_col, '=', '');
        }
        
        foreach($this->CI->s_categories_model->get_ids() as $category_id)
        {
            $categories_structure[] = array(
                'id' => $category_id,
                'level' => $level
            );
            
            $subcategories = (array)$this->get_categories_structure($category_id, $level + 1);
            if(count($subcategories) > 0) $categories_structure = array_merge($categories_structure, $subcategories);
        }
        
        return $categories_structure;
    }
    
    function get_category_levels($category_id = '')
    {
        return count($this->get_category_parents($category_id));
    }
    
    function get_category_parents($category_id = '')
    {
        $parents = array();
        
        while($this->category_has_parent($category_id))
        {
            $category_id = $this->get_category_parent($category_id);;
            $parents[] = $category_id;
        }
        
        return $parents;
    }
    
    function category_has_parent($category_id = '')
    {
        return (intval($this->get_category_parent($category_id)) > 0);
    }
    
    function get_category_parent($category_id = '')
    {
        if($this->category_exists($category_id))
        {
            return $this->CI->s_categories_model->get_item_data($category_id, $this->CI->s_categories_model->get_col('order_cat'));
        }
        else
        {
            return NULL;
        }
    }
    
    function category_exists($category_id = '')
    {
        return $this->CI->s_categories_model->item_exists($category_id);
    }
    
}