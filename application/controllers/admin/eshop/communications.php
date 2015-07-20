<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Communications extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('communications');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'), 80);
        $this->admin->form->col(__('col_3'));
        
        foreach($this->e_communications_model->get_data() as $communication)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $communication->id, $communication->_name));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($communication->public) ? '~/unpublish_communication/' . $communication->id : '~/publish_communication/' . $communication->id), (($communication->public) ? __('button_7') : __('button_8')), $communication->public));
            $this->admin->form->cell(admin_anchor('~/delete/' . $communication->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $communication->id), 'edit');
            if($communication->public) $contextmenu[] = array(__('button_9'), admin_url('~/unpublish_communication/' . $communication->id), 'x');
            else $contextmenu[] = array(__('button_10'), admin_url('~/publish_communication/' . $communication->id), 'check');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $communication->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($communication->id, 0, $this->cms->model->eshop_table('communications'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function unpublish_communication($comuunication_id = '')
    {
        if($this->e_communications_model->item_exists($comuunication_id))
        {
            $this->e_communications_model->set_item_data($comuunication_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_communication($comuunication_id = '')
    {
        if($this->e_communications_model->item_exists($comuunication_id))
        {
            $this->e_communications_model->set_item_data($comuunication_id, array('public' => TRUE));
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
            
            $data['_name'] = $this->input->post('_name');
            $data['public'] = is_form_true($this->input->post('public'));
            
            $this->e_communications_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('checkbox', 'public', __('field_2'), TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($communication_id = '')
    {
        if(!$this->e_communications_model->item_exists($communication_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['public'] = is_form_true($this->input->post('public'));
            
            $this->e_communications_model->set_item_data($communication_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_communications_model->$communication_id->_name);
        $this->admin->form->add_field('checkbox', 'public', __('field_2'), $this->e_communications_model->$communication_id->public);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($communication_id = '')
    {
        if($this->e_communications_model->item_exists($communication_id))
        {
            $this->e_communications_model->delete_item($communication_id);
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
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('public', __('field_2'), 'trim|intval');
    }
    
}