<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Taxes extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('taxes');
    }

    // TODO: Spravne to nezoraduje vypisy

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        
        foreach($this->e_taxes_model->get_data() as $tax)
        {
            $this->admin->form->cell(admin_anchor('~/edit/' . $tax->id, $tax->name));
            $this->admin->form->cell(doubleval($tax->tax) . '%');
            $this->admin->form->cell(admin_anchor('~/delete/' . $tax->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $tax->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $tax->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($tax->id, NULL, NULL, NULL, $contextmenu);
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
            $data['tax'] = $this->input->post('tax');
            
            $this->e_taxes_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', 'tax', __('field_2'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($tax_id = '')
    {
        if(!$this->e_taxes_model->item_exists($tax_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['tax'] = $this->input->post('tax');
            
            $this->e_taxes_model->set_item_data($tax_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->e_taxes_model->$tax_id->name);
        $this->admin->form->add_field('input', 'tax', __('field_2'), doubleval($this->e_taxes_model->$tax_id->tax));

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($tax_id = '')
    {
        if($this->e_taxes_model->item_exists($tax_id))
        {
            $this->e_taxes_model->delete_item($tax_id);
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
        $this->admin->form->set_rules('tax', __('field_2'), 'trim|required|tax|plus');
    }
    
}