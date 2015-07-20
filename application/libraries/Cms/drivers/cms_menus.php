<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Menus extends CI_Driver {
    
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->cms->model->load_system('menus');
        $this->CI->cms->model->load_system('menu_links');
    }
    
    function menu_exists($menu_id = '')
    {
        return (bool)$this->CI->s_menus_model->item_exists($menu_id);
    }
    
    function link_exists($link_id = '')
    {
        return (bool)$this->CI->s_menu_links_model->item_exists($link_id);
    }
    
    function is_link_in_menu($link_id = '', $menu_id = '')
    {
        if(!$this->link_exists($link_id)) return FALSE;
        return ($this->CI->s_menu_links_model->$link_id->menu_id == $menu_id);
    }
    
    function get_link_ids($menu_id = '')
    {
        if(!$this->menu_exists($menu_id)) return array();
        
        $this->CI->s_menu_links_model->where('menu_id', '=', $menu_id);
        return $this->CI->s_menu_links_model->get_ids();
    }
    
    function get_links($menu_id = '')
    {
        if(!$this->menu_exists($menu_id)) return array();
        
        $this->CI->s_menu_links_model->where('menu_id', '=', $menu_id);
        return $this->CI->s_menu_links_model->get_data();
    }
    
    // Parents
    
    function get_links_select_data($menu_id = '', $link_id = '', $unselectable = TRUE)
    {
        $this->CI->load->helper('string');
        $select_data = ($unselectable) ? array('') : array();
        
        foreach($this->get_links_structure($menu_id) as $link)
        {
            if(is_array($link) && $this->CI->s_menu_links_model->item_exists(@$link['id']) && (!$this->CI->s_menu_links_model->item_exists($link_id) || $this->valid_parent_link_id($link['id'], $link_id)))
            {
                $select_data[$link['id']] = repeater('-&nbsp;', @$link['level']) . $this->CI->s_menu_links_model->$link['id']->_text;
            }
        }
        
        return $select_data;
    }
    
    function valid_parent_link_id($parent_link_id = '', $link_id = '')
    {
        if(!$this->CI->s_menu_links_model->item_exists($link_id) || !$this->CI->s_menu_links_model->item_exists($parent_link_id)) return FALSE;
        if($link_id == $parent_link_id) return FALSE;
        if(in_array($link_id, $this->get_link_parents($parent_link_id))) return FALSE;
        return TRUE;
    }
    
    function get_links_structure($menu_id = '', $parent_id = NULL, $level = 0)
    {
        if(intval($menu_id) > 0 && !$this->menu_exists($menu_id)) return array();
        
        $links_structure = array();
        
        if((int)$parent_id > 0 && !$this->CI->s_menu_links_model->item_exists($parent_id)) return array();
        
        if(intval($menu_id) > 0) $this->CI->s_menu_links_model->where('menu_id', '=', $menu_id);
        
        $order_cat_col = $this->CI->s_menu_links_model->get_col('order_cat');
        
        if((int)$parent_id > 0)
        {
            $this->CI->s_menu_links_model->where($order_cat_col, '=', $parent_id);
        }
        else
        {
            $this->CI->s_menu_links_model->where($order_cat_col, '=', '');
        }
        
        foreach($this->CI->s_menu_links_model->get_ids() as $link_id)
        {
            $links_structure[] = array(
                'id' => $link_id,
                'level' => $level
            );
            
            $sublinks = (array)$this->get_links_structure($menu_id, $link_id, $level + 1);
            if(count($sublinks) > 0) $links_structure = array_merge($links_structure, $sublinks);
        }
        
        return $links_structure;
    }
    
    function get_link_levels($link_id = '')
    {
        return count($this->get_link_parents($link_id));
    }
    
    function get_link_parents($link_id = '')
    {
        $parents = array();
        
        while($this->link_has_parent($link_id))
        {
            $link_id = $this->get_link_parent($link_id);;
            $parents[] = $link_id;
        }
        
        return $parents;
    }
    
    function link_has_parent($link_id = '')
    {
        return (intval($this->get_link_parent($link_id)) > 0);
    }
    
    function get_link_parent($link_id = '')
    {
        if($this->link_exists($link_id))
        {
            return $this->CI->s_menu_links_model->get_item_data($link_id, $this->CI->s_menu_links_model->get_col('order_cat'));
        }
        else
        {
            return NULL;
        }
    }
    
}