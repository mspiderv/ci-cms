<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Href_attributes extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('href_attributes');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        
        foreach($this->s_href_attributes_model->get_data() as $href_attribute)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $href_attribute->id, $href_attribute->name));
            $this->admin->form->cell(admin_anchor('~/delete/' . $href_attribute->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $href_attribute->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $href_attribute->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($href_attribute->id, 0, $this->cms->model->system_table('href_attributes'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->s_href_attributes_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($href_attribute_id = '')
    {
        if(!$this->s_href_attributes_model->item_exists($href_attribute_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->s_href_attributes_model->set_item_data($href_attribute_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_href_attributes_model->$href_attribute_id->name);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($href_attribute_id = '')
    {
        if($this->s_href_attributes_model->item_exists($href_attribute_id))
        {
            $this->s_href_attributes_model->delete_item($href_attribute_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation()
    {
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|alpha|max_length[255]');
    }
    
}