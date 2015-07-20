<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_wraps extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('email_wraps');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        
        foreach($this->s_email_wraps_model->get_data() as $email_wrap)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $email_wrap->id, $email_wrap->name));
            $this->admin->form->cell(admin_anchor('~/delete/' . $email_wrap->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $email_wrap->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $email_wrap->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($email_wrap->id, 0, $this->cms->model->system_table('email_wraps'), NULL, $contextmenu);
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
            $data['_content'] = $this->input->post('_content');
            
            $this->s_email_wraps_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('ckeditor', '_content', __('field_2'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($email_wrap_id = '')
    {
        if(!$this->s_email_wraps_model->item_exists($email_wrap_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['_content'] = $this->input->post('_content');
            
            $this->s_email_wraps_model->set_item_data($email_wrap_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_email_wraps_model->$email_wrap_id->name);
        $this->admin->form->add_field('ckeditor', '_content', __('field_2'), $this->s_email_wraps_model->$email_wrap_id->_content);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($email_wrap_id = '')
    {
        if($this->s_email_wraps_model->item_exists($email_wrap_id))
        {
            $this->s_email_wraps_model->delete_item($email_wrap_id);
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
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('_content', __('field_2'), 'trim|required');
    }
    
}