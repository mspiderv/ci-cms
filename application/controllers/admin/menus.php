<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menus extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('menus');
        $this->cms->model->load_system('menu_links');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add_menu', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1_1'));
        $this->admin->form->col(__('col_1_2'));
        
        foreach($this->s_menus_model->get_data() as $menu)
        {
            $options_cell = '';
            $options_cell .= admin_anchor('~/add_link/' . $menu->id, __('button_7'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete_menu/' . $menu->id, __('button_2'), __('confirm_1'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit_menu/' . $menu->id, $menu->name));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit_menu/' . $menu->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_menu/' . $menu->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($menu->id, 0, $this->cms->model->system_table('menus'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    // Menus
    
    function add_menu()
    {
        $this->_validation_menu();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->s_menus_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_menu($menu_id = '')
    {
        if(!$this->s_menus_model->item_exists($menu_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_menu();
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->s_menus_model->$menu_id->name,
            'href' => admin_url('~/edit_menu/' . $menu_id)
        ));
        
        $this->admin->form->button_admin_link('~/add_link/' . $menu_id, __('button_9'), 'plus');
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->s_menus_model->set_item_data($menu_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_menus_model->$menu_id->name);

        $this->admin->form->col(__('col_2_1'));
        $this->admin->form->col(__('col_2_2'));
        $this->admin->form->col(__('col_2_3'));
        
        foreach($this->cms->menus->get_links_structure($menu_id) as $link)
        {
            $link_id = @$link['id'];
            $link_level = @$link['level'];
            $link = $this->s_menu_links_model->get_item($link_id);
            
            $this->admin->form->cell_left(admin_anchor('~/edit_link/' . $link->id, $link->_text));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($link->public) ? '~/unpublish_link/' : '~/publish_link/') . $link->id, (($link->public) ? __('button_11') : __('button_12')), $link->public));
            $this->admin->form->cell(admin_anchor('~/delete_link/' . $link->id, __('button_2'), __('confirm_2')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit_link/' . $link->id), 'edit');
            
            if($link->public) $contextmenu[] = array(__('button_11'), admin_url('~/unpublish_link/' . $link->id), 'x');
            else $contextmenu[] = array(__('button_12'), admin_url('~/publish_link/' . $link->id), 'check');
            
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_link/' . $link->id), 'delete', __('confirm_2'));
            
            $this->admin->form->row($link->id, $link_level, $this->cms->model->system_table('menu_links') . '_' . intval($link->parent_link_id), TRUE, $contextmenu);
        }
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function unpublish_link($link_id = '')
    {
        $redirect = '';
        
        if($this->s_menu_links_model->item_exists($link_id))
        {
            $redirect = '~/edit_menu/' . $this->s_menu_links_model->$link_id->menu_id;
            $this->s_menu_links_model->set_item_data($link_id, array('public' => FALSE));
            $this->admin->form->message(__('message_7'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_6'), TRUE);
        }
        admin_redirect($redirect);
    }
    
    function publish_link($link_id = '')
    {
        $redirect = '';
        
        if($this->s_menu_links_model->item_exists($link_id))
        {
            $redirect = '~/edit_menu/' . $this->s_menu_links_model->$link_id->menu_id;
            $this->s_menu_links_model->set_item_data($link_id, array('public' => TRUE));
            $this->admin->form->message(__('message_8'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_7'), TRUE);
        }
        admin_redirect($redirect);
    }
    
    function delete_menu($menu_id = '')
    {
        if($this->s_menus_model->item_exists($menu_id))
        {
            $this->s_menus_model->delete_item($menu_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_menu()
    {
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
    }
    
    // Menu links
    
    function add_link($menu_id = '')
    {
        if(!$this->s_menus_model->item_exists($menu_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->s_menus_model->$menu_id->name,
            'href' => admin_url('~/edit_menu/' . $menu_id)
        ));
        
        $this->_validation_link($menu_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['menu_id'] = $menu_id;
            $data['_text'] = $this->input->post('_text');
            $data['href'] = $this->input->post('href');
            $data['public'] = is_form_true($this->input->post('public'));
            $data['parent_link_id'] = $this->input->post('parent_link_id');
            
            $this->s_menu_links_model->add_item($data);
            $this->admin->form->message(__('message_4'), TRUE);
            admin_redirect('~/edit_menu/' . $menu_id);
        }
        
        $this->admin->form->add_field('input', '_text', __('field_2'));
        $this->admin->form->add_field('href', 'href', __('field_3'));
        $this->admin->form->add_field('select', 'parent_link_id', __('field_4'), $this->cms->menus->get_links_select_data($menu_id), '', TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_5'), TRUE);
        
        $this->admin->form->button_submit(__('button_8'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_link($link_id = '')
    {
        if(!$this->s_menu_links_model->item_exists($link_id))
        {
            $this->admin->form->error(__('error_4'), TRUE);
            admin_redirect();
        }
        
        $menu_id = $this->s_menu_links_model->$link_id->menu_id;
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->s_menus_model->$menu_id->name,
            'href' => admin_url('~/edit_menu/' . $menu_id)
        ));
        
        $this->_validation_link($menu_id, $link_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_text'] = $this->input->post('_text');
            $data['href'] = $this->input->post('href');
            $data['public'] = is_form_true($this->input->post('public'));
            $data['parent_link_id'] = $this->input->post('parent_link_id');
            
            $this->s_menu_links_model->set_item_data($link_id, $data);
            $this->admin->form->message(__('message_5'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect('~/edit_menu/' . $menu_id);
        }
        
        $this->admin->form->add_field('input', '_text', __('field_2'), $this->s_menu_links_model->$link_id->_text);
        $this->admin->form->add_field('href', 'href', __('field_3'), $this->s_menu_links_model->$link_id->href);
        $this->admin->form->add_field('select', 'parent_link_id', __('field_4'), $this->cms->menus->get_links_select_data($menu_id, $link_id), $this->s_menu_links_model->$link_id->parent_link_id, TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_5'), $this->s_menu_links_model->$link_id->public);

        $this->admin->form->button_submit(__('button_10'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_admin_link('~/edit_menu/' . $menu_id, $this->s_menus_model->$menu_id->name, 'arrowreturnthick-1-w');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_link($link_id = '')
    {
        $redirect = '';
        
        if($this->s_menu_links_model->item_exists($link_id))
        {
            $redirect = '~/edit_menu/' . $this->s_menu_links_model->$link_id->menu_id;
            
            $this->s_menu_links_model->delete_item($link_id);
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect($redirect);
    }
    
    protected function _validation_link($menu_id = '', $link_id = '')
    {
        $parent_link_validation = (intval($link_id) > 0) ? 'parent_link_id[' . $link_id . ']' : 'menu_link[' . intval($menu_id) . ']';
        
        $this->admin->form->set_rules('_text', __('field_2'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('href', __('field_3'), 'trim|href|required_href');
        $this->admin->form->set_rules('parent_link_id', __('field_4'), 'trim|' . $parent_link_validation);
        $this->admin->form->set_rules('public', __('field_5'), 'trim|intval');
    }
    
}