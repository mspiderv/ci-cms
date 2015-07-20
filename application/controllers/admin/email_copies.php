<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_copies extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('email_copies');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        
        foreach($this->s_email_copies_model->get_data() as $email_copy)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $email_copy->id, $email_copy->email));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($email_copy->hidden) ? '~/show_copy/' : '~/hide_copy/') . $email_copy->id, (($email_copy->hidden) ? __('button_7') : __('button_8')), $email_copy->hidden));
            $this->admin->form->cell(admin_anchor('~/delete/' . $email_copy->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $email_copy->id), 'edit');
            
            if($email_copy->hidden) $contextmenu[] = array(__('button_8'), admin_url('~/show_copy/' . $email_copy->id), 'show');
            else $contextmenu[] = array(__('button_7'), admin_url('~/hide_copy/' . $email_copy->id), 'x');
            
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $email_copy->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($email_copy->id, 0, $this->cms->model->system_table('email_copies'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function show_copy($copy_id = '')
    {
        if($this->s_email_copies_model->item_exists($copy_id))
        {
            $this->s_email_copies_model->set_item_data($copy_id, array('hidden' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function hide_copy($copy_id = '')
    {
        if($this->s_email_copies_model->item_exists($copy_id))
        {
            $this->s_email_copies_model->set_item_data($copy_id, array('hidden' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['email'] = $this->input->post('email');
            $data['hidden'] = is_form_true($this->input->post('hidden'));
            
            $this->s_email_copies_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'email', __('field_1'));
        $this->admin->form->add_field('checkbox', 'hidden', __('field_2'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($email_copy_id = '')
    {
        if(!$this->s_email_copies_model->item_exists($email_copy_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['email'] = $this->input->post('email');
            $data['hidden'] = is_form_true($this->input->post('hidden'));
            
            $this->s_email_copies_model->set_item_data($email_copy_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'email', __('field_1'), $this->s_email_copies_model->$email_copy_id->email);
        $this->admin->form->add_field('checkbox', 'hidden', __('field_2'), is_form_true($this->s_email_copies_model->$email_copy_id->hidden));

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($email_copy_id = '')
    {
        if($this->s_email_copies_model->item_exists($email_copy_id))
        {
            $this->s_email_copies_model->delete_item($email_copy_id);
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
        $this->admin->form->set_rules('email', __('field_1'), 'trim|required|valid_email|max_length[255]');
        $this->admin->form->set_rules('hidden', __('field_2'), 'intval');
    }
    
}