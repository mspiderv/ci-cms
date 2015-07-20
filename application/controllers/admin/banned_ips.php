<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banned_ips extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('banned_ips');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        
        foreach($this->s_banned_ips_model->get_data() as $banned_ip)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $banned_ip->id, $banned_ip->ip));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($banned_ip->active) ? '~/unactive_banned_ip/' : '~/active_banned_ip/') . $banned_ip->id, (($banned_ip->active) ? __('button_7') : __('button_8')), $banned_ip->active));
            $this->admin->form->cell(admin_anchor('~/delete/' . $banned_ip->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $banned_ip->id), 'edit');
            if($banned_ip->active) $contextmenu[] = array(__('button_7'), admin_url('~/unactive_banned_ip/' . $banned_ip->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/active_banned_ip/' . $banned_ip->id), 'check');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $banned_ip->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($banned_ip->id, 0, '', NULL, $contextmenu);
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    function unactive_banned_ip($banned_ip_id = '')
    {
        if($this->s_banned_ips_model->item_exists($banned_ip_id))
        {
            $this->s_banned_ips_model->set_item_data($banned_ip_id, array('active' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function active_banned_ip($banned_ip_id = '')
    {
        if($this->s_banned_ips_model->item_exists($banned_ip_id))
        {
            $this->s_banned_ips_model->set_item_data($banned_ip_id, array('active' => TRUE));
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
            
            $data['ip'] = $this->input->post('ip');
            $data['active'] = is_form_true($this->input->post('active'));
            
            $this->s_banned_ips_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'ip', __('field_1'));
        $this->admin->form->add_field('checkbox', 'active', __('field_2'), TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($banned_ip_id = '')
    {
        if(!$this->s_banned_ips_model->item_exists($banned_ip_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['ip'] = $this->input->post('ip');
            $data['active'] = is_form_true($this->input->post('active'));
            
            $this->s_banned_ips_model->set_item_data($banned_ip_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'ip', __('field_1'), $this->s_banned_ips_model->$banned_ip_id->ip);
        $this->admin->form->add_field('checkbox', 'active', __('field_2'), $this->s_banned_ips_model->$banned_ip_id->active);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($banned_ip_id = '')
    {
        if($this->s_banned_ips_model->item_exists($banned_ip_id))
        {
            $this->s_banned_ips_model->delete_item($banned_ip_id);
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
        $this->admin->form->set_rules('ip', __('field_1'), 'trim|required|valid_ip|max_length[15]');
        $this->admin->form->set_rules('active', __('field_2'), 'intval');
    }
    
}