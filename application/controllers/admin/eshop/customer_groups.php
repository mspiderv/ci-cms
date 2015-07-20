<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer_groups extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('customer_groups');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add#tab-1', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->e_customer_groups_model->get_data() as $customer_group)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $customer_group->id, $customer_group->_name));
            $this->admin->form->cell($customer_group->coef);
            $this->admin->form->cell($this->admin->form->cell_radio('~/set_default_customer_group/' . $customer_group->id, __('button_7'), (db_config('default_customer_group_id') ==  $customer_group->id)));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $customer_group->id), 'edit');
            
            if(db_config('default_customer_group_id') ==  $customer_group->id)
            {
                $this->admin->form->cell();
            }
            else
            {
                $this->admin->form->cell(admin_anchor('~/delete/' . $customer_group->id, __('button_2'), __('confirm_1')));
                $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $customer_group->id), 'delete', __('confirm_1'));
            }
            
            $this->admin->form->row($customer_group->id, 0, $this->cms->model->eshop_table('customer_groups'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function set_default_customer_group($customer_group_id = '')
    {
        if($this->e_customer_groups_model->item_exists($customer_group_id))
        {
            db_config('default_customer_group_id', $customer_group_id);
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function add()
    {
        $this->_validation(TRUE);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['coef'] = $this->input->post('coef');
            
            $this->e_customer_groups_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('slider', 'coef', __('field_2'), 1, 0, 10, 0.01);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($customer_group_id = '')
    {
        if(!$this->e_customer_groups_model->item_exists($customer_group_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['coef'] = $this->input->post('coef');
            
            $this->e_customer_groups_model->set_item_data($customer_group_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_customer_groups_model->$customer_group_id->_name);
        $this->admin->form->add_field('slider', 'coef', __('field_2'), $this->e_customer_groups_model->$customer_group_id->coef, 0, 10, 0.01);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($customer_group_id = '')
    {
        if($this->e_customer_groups_model->item_exists($customer_group_id))
        {
            if(db_config('default_customer_group_id') == $customer_group_id)
            {
                $this->admin->form->error(__('error_4'), TRUE);
            }
            else
            {
                $this->e_customer_groups_model->delete_item($customer_group_id);
                $this->admin->form->message(__('message_3'), TRUE);
            }
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
        $this->admin->form->set_rules('coef', __('field_2'), 'trim|required|numeric|plus');
    }
    
}