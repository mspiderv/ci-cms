<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Pages extends CI_Driver {
    
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->cms->model->load_system('pages');
        $this->CI->cms->model->load_system('page_types');
        $this->CI->cms->model->load_system('page_type_variables');
        $this->CI->cms->model->load_system('categories');
        $this->CI->cms->model->load_system('pages_in_categories');
    }
    
    /* Page types */
    
    function get_page_type_variables($page_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_page_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_page_type_variables_model->where('edit', '=', '1');
        $this->CI->s_page_type_variables_model->where('page_type_id', '=', $page_type_id);
        return $this->CI->s_page_type_variables_model->get_data();
    }
    
    function get_page_type_variable_names($page_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_page_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_page_type_variables_model->where('edit', '=', '1');
        $this->CI->s_page_type_variables_model->where('page_type_id', '=', $page_type_id);
        return $this->CI->s_page_type_variables_model->get_data_in_col('name');
    }
    
    function get_page_type_variable_ids($page_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_page_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_page_type_variables_model->where('edit', '=', '1');
        $this->CI->s_page_type_variables_model->where('page_type_id', '=', $page_type_id);
        return $this->CI->s_page_type_variables_model->get_ids();
    }
    
    function get_page_type_variable($page_type_variable_id = '')
    {
        if($this->CI->s_page_type_variables_model->item_exists($page_type_variable_id))
        {
            return $this->CI->s_page_type_variables_model->$page_type_variable_id;
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_page_type_variable_field_row($page_type_variable_id = '')
    {
        if($this->CI->s_page_type_variables_model->item_exists($page_type_variable_id))
        {
            $this->CI->admin->form->info($this->CI->s_page_type_variables_model->$page_type_variable_id->info);
            return $this->CI->admin->form->get_field_row($this->CI->s_page_type_variables_model->$page_type_variable_id->field_type, $this->CI->s_page_type_variables_model->$page_type_variable_id->name, $this->CI->s_page_type_variables_model->$page_type_variable_id->title);
        }
        else
        {
            return FALSE;
        }
    }
    
    function add_page_type_variable_field($page_type_variable_id = '', $value = '')
    {
        if($this->CI->s_page_type_variables_model->item_exists($page_type_variable_id))
        {
            $this->CI->admin->form->info($this->CI->s_page_type_variables_model->$page_type_variable_id->info);
            return $this->CI->admin->form->add_dynamic_field($this->CI->s_page_type_variables_model->$page_type_variable_id->field_type, $this->CI->s_page_type_variables_model->$page_type_variable_id->name, $this->CI->s_page_type_variables_model->$page_type_variable_id->title, $value);
        }
        else
        {
            return FALSE;
        }
    }
    
    function load_page_type_model($page_type_id = '', $model = 'page_type_data')
    {
        if($this->CI->s_page_types_model->item_exists($page_type_id))
        {
            $this->CI->cms->model->load_user('page_type_data_' . $page_type_id, $model);
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /* Pages */
    
    function load_page_model($page_id = '', $model = 'page_type_data')
    {
        if($this->CI->s_pages_model->item_exists($page_id))
        {
            return $this->load_page_type_model($this->CI->s_pages_model->$page_id->page_type_id, $model);
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_page_data($page_id = '', $variable = '')
    {
        if($this->load_page_model($page_id) === FALSE) return FALSE;
        if(!$this->CI->u_page_type_data_model->item_exists($page_id)) return FALSE;
        
        $page_data = (object) array_merge((array)$this->CI->s_pages_model->get_item($page_id), (array)$this->CI->u_page_type_data_model->get_item($page_id));
        
        if(strlen($variable) > 0)
        {
            return $page_data->$variable;
        }
        else
        {
            return $page_data;
        }
    }
    
    function set_page_data($page_id = '', $data = array())
    {
        if($this->load_page_model($page_id) === FALSE) return FALSE;
        if($this->CI->u_page_type_data_model->item_exists($page_id))
        {
            $this->CI->u_page_type_data_model->set_item_data($page_id, $data);
        }
        else
        {
            $data[$this->CI->u_page_type_data_model->get_col('id')] = $page_id;
            $this->CI->u_page_type_data_model->add_item($data);
        }
    }
    
    function duplicate($page_id = '')
    {
        if(!$this->CI->s_pages_model->item_exists($page_id)) return FALSE;
        
        $status = TRUE;
        
        $page_data = (array)$this->CI->s_pages_model->get_item($page_id);
        unset($page_data[$this->CI->s_pages_model->get_col('order')]);
        unset($page_data[$this->CI->s_pages_model->get_col('id')]);
        $page_data['lastmod'] = time();
        $this->CI->s_pages_model->add_item($page_data);
        
        $new_page_id = $this->CI->s_pages_model->insert_id();
        
        $this->load_page_type_model($page_data['page_type_id']);
        $data = (array)$this->CI->u_page_type_data_model->get_item($page_id);
        $data[$this->CI->u_page_type_data_model->get_col('id')] = $new_page_id;
        if(!$this->CI->u_page_type_data_model->add_item($data)) $status = FALSE;
        
        $this->set_page_categories($new_page_id, $this->get_page_categories($page_id));
        
        return $status;
    }
    
    // Parents
    
    function get_pages_select_data($page_id = '', $unselectable = TRUE)
    {
        $this->CI->load->helper('string');
        $select_data = ($unselectable) ? array('') : array();
        
        foreach($this->get_pages_structure() as $page)
        {
            if(is_array($page) && $this->CI->s_pages_model->item_exists(@$page['id']) && (!$this->CI->s_pages_model->item_exists($page_id) || $this->valid_parent_page_id($page['id'], $page_id)))
            {
                $select_data[$page['id']] = repeater('-&nbsp;', @$page['level']) . $this->CI->s_pages_model->$page['id']->name;
            }
        }
        
        return $select_data;
    }
    
    function valid_parent_page_id($parent_page_id = '', $page_id = '')
    {
        if(!$this->CI->s_pages_model->item_exists($page_id) || !$this->CI->s_pages_model->item_exists($parent_page_id)) return FALSE;
        if($page_id == $parent_page_id) return FALSE;
        if(in_array($page_id, $this->get_page_parents($parent_page_id))) return FALSE;
        return TRUE;
    }
    
    function get_pages_structure($parent_id = NULL, $level = 0)
    {
        $pages_structure = array();
        
        if((int)$parent_id > 0 && !$this->CI->s_pages_model->item_exists($parent_id)) return array();
        
        $order_cat_col = $this->CI->s_pages_model->get_col('order_cat');
        
        if((int)$parent_id > 0)
        {
            $this->CI->s_pages_model->where($order_cat_col, '=', $parent_id);
        }
        else
        {
            $this->CI->s_pages_model->where($order_cat_col, '=', '');
        }
        
        foreach($this->CI->s_pages_model->get_ids() as $page_id)
        {
            $pages_structure[] = array(
                'id' => $page_id,
                'level' => $level
            );
            
            $subpages = (array)$this->get_pages_structure($page_id, $level + 1);
            if(count($subpages) > 0) $pages_structure = array_merge($pages_structure, $subpages);
        }
        
        return $pages_structure;
    }
    
    function get_page_levels($page_id = '')
    {
        return count($this->get_page_parents($page_id));
    }
    
    function get_page_parents($page_id = '')
    {
        $parents = array();
        
        while($this->page_has_parent($page_id))
        {
            $page_id = $this->get_page_parent($page_id);;
            $parents[] = $page_id;
        }
        
        return $parents;
    }
    
    function page_has_parent($page_id = '')
    {
        return (intval($this->get_page_parent($page_id)) > 0);
    }
    
    function get_page_parent($page_id = '')
    {
        if($this->page_exists($page_id))
        {
            return $this->CI->s_pages_model->get_item_data($page_id, $this->CI->s_pages_model->get_col('order_cat'));
        }
        else
        {
            return NULL;
        }
    }
    
    function page_exists($page_id = '')
    {
        return $this->CI->s_pages_model->item_exists($page_id);
    }
    
    /* Page categories */
    
    function add_page_to_category($page_id = '', $category_id = '')
    {
        if($this->CI->s_pages_model->item_exists($page_id))
        {
            if($this->CI->s_categories_model->item_exists($category_id))
            {
                $this->CI->s_pages_in_categories_model->where('page_id', '=', $page_id);

                if(in_array($category_id, $this->CI->s_pages_in_categories_model->get_data_in_col('category_id'))) return TRUE;

                $new_page_in_category = array();

                $new_page_in_category['page_id'] = $page_id;
                $new_page_in_category['category_id'] = $category_id;

                return $this->CI->s_pages_in_categories_model->add_item($new_page_in_category);
            }
            else
            {
                show_error('Stránku s ID <strong>' . $page_id . '</strong> sa nepodarilo pridať do kategórie s ID <strong>' . $category_id . '</strong>, pretože táto kategória neexistuje.');
            }
        }
        else
        {
            show_error('Stránku s ID <strong>' . $page_id . '</strong> sa nepodarilo pridať do kategórie s ID <strong>' . $category_id . '</strong>, pretože táto stránka neexistuje.');
        }
    }
    
    function delete_page_from_category($page_id = '', $category_id = '')
    {
        if($this->CI->s_pages_model->item_exists($page_id) && $this->CI->s_categories_model->item_exists($category_id))
        {
            $this->CI->s_pages_in_categories_model->where('page_id', '=', $page_id);
            $this->CI->s_pages_in_categories_model->where('category_id', '=', $category_id);
            $this->CI->s_pages_in_categories_model->delete();
            
            return TRUE;
        }
        else
        {
            return TRUE;
        }
    }
    
    function get_page_categories($page_id = '')
    {
        if($this->CI->s_pages_model->item_exists($page_id))
        {
            $this->CI->s_pages_in_categories_model->where('page_id', '=', $page_id);
            return $this->CI->s_pages_in_categories_model->get_data_in_col('category_id');
        }
        else
        {
            return array();
        }
    }
    
    function is_page_in_category($page_id = '', $category_id = '')
    {
        return in_array($category_id, $this->get_page_categories($page_id));
    }
    
    function set_page_categories($page_id = '', $page_category_ids = array())
    {
        if($this->CI->s_pages_model->item_exists($page_id))
        {
            $already_in = $this->get_page_categories($page_id);
            
            // Add to categories
            foreach(array_diff($page_category_ids, $already_in) as $add_to_category_id)
            {
                $this->add_page_to_category($page_id, $add_to_category_id);
            }
            
            // Delete from categories
            foreach(array_diff($already_in, $page_category_ids) as $delete_from_category_id)
            {
                $this->delete_page_from_category($page_id, $delete_from_category_id);
            }

            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
}