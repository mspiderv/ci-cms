<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redirects extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('redirects');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->s_redirects_model->get_data() as $redirect)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $redirect->id, $redirect->from));
            $this->admin->form->cell_left($redirect->to);
            $this->admin->form->cell($this->admin->form->cell_checkbox((($redirect->active) ? '~/deactive_redirect/' : '~/active_redirect/') . $redirect->id, (($redirect->active) ? __('button_7') : __('button_8')), $redirect->active));
            $this->admin->form->cell(admin_anchor('~/delete/' . $redirect->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $redirect->id), 'edit');
            if($redirect->active) $contextmenu[] = array(__('button_7'), admin_url('~/deactive_redirect/' . $redirect->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/active_redirect/' . $redirect->id), 'check');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $redirect->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($redirect->id, 0, $this->cms->model->system_table('redirects'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function deactive_redirect($redirect_id = '')
    {
        if($this->s_redirects_model->item_exists($redirect_id))
        {
            $this->s_redirects_model->set_item_data($redirect_id, array('active' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function active_redirect($redirect_id = '')
    {
        if($this->s_redirects_model->item_exists($redirect_id))
        {
            $this->s_redirects_model->set_item_data($redirect_id, array('active' => TRUE));
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
            
            $data['from'] = $this->input->post('from');
            $data['to'] = $this->input->post('to');
            $data['active'] = is_form_true($this->input->post('active'));
            
            $this->s_redirects_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'from', __('field_1'));
        $this->admin->form->add_field('input', 'to', __('field_2'));
        $this->admin->form->add_field('checkbox', 'active', __('field_3'), TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($redirect_id = '')
    {
        if(!$this->s_redirects_model->item_exists($redirect_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['from'] = $this->input->post('from');
            $data['to'] = $this->input->post('to');
            $data['active'] = is_form_true($this->input->post('active'));
            
            $this->s_redirects_model->set_item_data($redirect_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'from', __('field_1'), $this->s_redirects_model->$redirect_id->from);
        $this->admin->form->add_field('input', 'to', __('field_2'), $this->s_redirects_model->$redirect_id->to);
        $this->admin->form->add_field('checkbox', 'active', __('field_3'), $this->s_redirects_model->$redirect_id->active);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($redirect_id = '')
    {
        if($this->s_redirects_model->item_exists($redirect_id))
        {
            $this->s_redirects_model->delete_item($redirect_id);
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
        $this->admin->form->set_rules('from', __('field_1'), 'trim|required');
        $this->admin->form->set_rules('to', __('field_2'), 'trim|required');
        $this->admin->form->set_rules('active', __('field_3'), 'trim|intval');
    }
    
}