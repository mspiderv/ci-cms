<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Categories extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('categories');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add#tab-1', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        
        foreach($this->cms->categories->get_categories_structure() as $category)
        {
            $category_id = @$category['id'];
            $category_level = @$category['level'];
            
            $options_cell = '';
            $options_cell .= admin_anchor('~/add/' . $category_id, __('button_8'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $category_id, __('button_2'), __('confirm_1'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $category_id, $this->s_categories_model->$category_id->_name));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($this->s_categories_model->$category_id->public) ? '~/unpublish_category/' : '~/publish_category/') . $category_id, (($this->s_categories_model->$category_id->public) ? __('button_6') : __('button_7')), $this->s_categories_model->$category_id->public));
            $this->admin->form->cell($options_cell);
            
            $contextmenu = array();
            
            $contextmenu[] = array(__('button_4'), admin_url('~/edit/' . $category_id), 'edit');
            $contextmenu[] = array(__('button_8'), admin_url('~/add/' . $category_id), 'add');
            
            if($this->s_categories_model->$category_id->public) $contextmenu[] = array(__('button_6'), admin_url('~/unpublish_category/' . $category_id), 'x');
            else $contextmenu[] = array(__('button_7'), admin_url('~/publish_category/' . $category_id), 'check');
            
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $category_id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($category_id, $category_level, $this->cms->model->system_table('categories') . '_' . $this->s_categories_model->$category_id->parent_id, TRUE, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function unpublish_category($category_id = '')
    {
        if($this->s_categories_model->item_exists($category_id))
        {
            $this->s_categories_model->set_item_data($category_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_category($category_id = '')
    {
        if($this->s_categories_model->item_exists($category_id))
        {
            $this->s_categories_model->set_item_data($category_id, array('public' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function add($parent_category_id = '')
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $parent_id = (int)$this->input->post('parent_id');
            $parent_id = ($this->s_categories_model->item_exists($parent_id)) ? $parent_id : NULL;
            
            $data['_name'] = $this->input->post('_name');
            $data['parent_id'] = $parent_id;
            $data['public'] = is_form_true($this->input->post('public'));
            $data['_meta_title'] = $this->input->post('_meta_title');
            $data['_meta_description'] = $this->input->post('_meta_description');
            $data['_meta_keywords'] = $this->input->post('_meta_keywords');
            
            $this->s_categories_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('select', 'parent_id', __('field_3'), $this->cms->categories->get_categories_select_data(), $parent_category_id, TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_4'), TRUE);
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', '_meta_title', __('field_11'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_12'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_13'));
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }

    function edit($category_id = '')
    {
        if(!$this->s_categories_model->item_exists($category_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation($category_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $parent_id = (int)$this->input->post('parent_id');
            $parent_id = ($this->s_categories_model->item_exists($parent_id)) ? $parent_id : NULL;
            
            $data['_name'] = $this->input->post('_name');
            $data['parent_id'] = $parent_id;
            $data['public'] = is_form_true($this->input->post('public'));
            $data['_meta_title'] = $this->input->post('_meta_title');
            $data['_meta_description'] = $this->input->post('_meta_description');
            $data['_meta_keywords'] = $this->input->post('_meta_keywords');
            
            $this->s_categories_model->set_item_data($category_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->s_categories_model->$category_id->_name);
        $this->admin->form->add_field('select', 'parent_id', __('field_3'), $this->cms->categories->get_categories_select_data($category_id), $this->s_categories_model->$category_id->parent_id, TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_4'), (bool)$this->s_categories_model->$category_id->public);
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', '_meta_title', __('field_11'), $this->s_categories_model->$category_id->_meta_title);
        $this->admin->form->add_field('textarea', '_meta_description', __('field_12'), $this->s_categories_model->$category_id->_meta_description);
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_13'), $this->s_categories_model->$category_id->_meta_keywords);
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_submit(__('button_5'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($category_id = '')
    {
        if($this->s_categories_model->item_exists($category_id))
        {
            $this->s_categories_model->delete_item($category_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($category_id = '')
    {
        $parent_category_validation = (intval($category_id) > 0) ? 'system_parent_category_id[' . $category_id . ']' : 'item_exists_system[categories]';
        
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('parent_id', __('field_3'), 'trim|' . $parent_category_validation);
        $this->admin->form->set_rules('public', __('field_4'), 'trim|intval');
        $this->admin->form->set_rules('_meta_title', __('field_11'), 'trim|max_length[255]');
        $this->admin->form->set_rules('_meta_description', __('field_12'), 'trim');
        $this->admin->form->set_rules('_meta_keywords', __('field_13'), 'trim');
    }
    
}