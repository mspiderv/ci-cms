<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Distributors extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('distributors');
    }

    // TODO: Spravne to nezoraduje vypisy
    // TODO: Dorobit premennu "dodacia lehota" alebo tak nieco

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        
        foreach($this->e_distributors_model->get_data() as $distributor)
        {
            $this->admin->form->cell(admin_anchor('~/edit/' . $distributor->id, $distributor->name));
            $this->admin->form->cell(admin_anchor('~/delete/' . $distributor->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $distributor->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $distributor->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($distributor->id, NULL, NULL, NULL, $contextmenu);
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
            
            $this->e_distributors_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($distributor_id = '')
    {
        if(!$this->e_distributors_model->item_exists($distributor_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->e_distributors_model->set_item_data($distributor_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->e_distributors_model->$distributor_id->name);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($distributor_id = '')
    {
        if($this->e_distributors_model->item_exists($distributor_id))
        {
            $this->e_distributors_model->delete_item($distributor_id);
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
    }
    
}