<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Emails extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('emails');
        $this->cms->model->load_system('email_wraps');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->s_emails_model->get_data() as $email)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $email->id, $email->name));
            $this->admin->form->cell_left($email->_subject);
            $this->admin->form->cell_left(admin_anchor('email_wraps/edit/' . $email->email_wrap_id, $this->s_email_wraps_model->get_item_data($email->email_wrap_id, 'name')));
            $this->admin->form->cell(admin_anchor('~/delete/' . $email->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $email->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $email->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($email->id, 0, $this->cms->model->system_table('emails'), NULL, $contextmenu);
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
            $data['email_wrap_id'] = $this->input->post('email_wrap_id');
            $data['_subject'] = $this->input->post('_subject');
            $data['_content'] = $this->input->post('_content');
            
            $this->s_emails_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('select_email_wrap', 'email_wrap_id', __('field_2'), '', TRUE);
        $this->admin->form->add_field('input', '_subject', __('field_3'));
        $this->admin->form->add_field('ckeditor', '_content', __('field_4'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($email_id = '')
    {
        if(!$this->s_emails_model->item_exists($email_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['email_wrap_id'] = $this->input->post('email_wrap_id');
            $data['_subject'] = $this->input->post('_subject');
            $data['_content'] = $this->input->post('_content');
            
            $this->s_emails_model->set_item_data($email_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_emails_model->$email_id->name);
        $this->admin->form->add_field('select_email_wrap', 'email_wrap_id', __('field_2'), $this->s_emails_model->$email_id->email_wrap_id, TRUE);
        $this->admin->form->add_field('input', '_subject', __('field_3'), $this->s_emails_model->$email_id->_subject);
        $this->admin->form->add_field('ckeditor', '_content', __('field_4'), $this->s_emails_model->$email_id->_content);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($email_id = '')
    {
        if($this->s_emails_model->item_exists($email_id))
        {
            $this->s_emails_model->delete_item($email_id);
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
        $this->admin->form->set_rules('email_wrap_id', __('field_2'), 'trim|item_exists_system[email_wraps]');
        $this->admin->form->set_rules('_subject', __('field_3'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('_content', __('field_4'), 'trim|required');
    }
    
}