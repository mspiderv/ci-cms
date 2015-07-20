<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_parameters extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('product_parameters');
        $this->cms->model->load_eshop('product_parameter_groups');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add_parameter_group', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        
        foreach($this->e_product_parameter_groups_model->get_data() as $product_parameter_group)
        {
            $options_cell  = admin_anchor('~/add_parameter/' . $product_parameter_group->id, __('button_8'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete_parameter_group/' . $product_parameter_group->id, __('button_2'), __('confirm_2'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit_parameter_group/' . $product_parameter_group->id, $product_parameter_group->name));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_8'), admin_url('~/add_parameter/' . $product_parameter_group->id), 'add');
            $contextmenu[] = array(__('button_3'), admin_url('~/edit_parameter_group/' . $product_parameter_group->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_parameter_group/' . $product_parameter_group->id), 'delete', __('confirm_2'));
            
            $this->admin->form->row($product_parameter_group->id, 0, $this->cms->model->eshop_table('product_parameter_groups'), FALSE, $contextmenu);
            
            $this->e_product_parameters_model->where('product_parameter_group_id', '=', $product_parameter_group->id);
            foreach($this->e_product_parameters_model->get_data() as $product_parameter)
            {
                $this->admin->form->cell_left(admin_anchor('~/edit_parameter/' . $product_parameter->id, $product_parameter->_name));
                $this->admin->form->cell(admin_anchor('~/delete_parameter/' . $product_parameter->id, __('button_2'), __('confirm_1')));

                $contextmenu = array();

                $contextmenu[] = array(__('button_3'), admin_url('~/edit_parameter/' . $product_parameter->id), 'edit');
                $contextmenu[] = array(__('button_2'), admin_url('~/delete_parameter/' . $product_parameter->id), 'delete', __('confirm_1'));

                $this->admin->form->row($product_parameter->id, 1, $this->cms->model->eshop_table('product_parameters_' . $product_parameter_group->id), TRUE, $contextmenu);
            }
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    // Skupiny parametrov
    
    function add_parameter_group()
    {
        $this->_validation_parameter_group();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->e_product_parameter_groups_model->add_item($data);
            $this->admin->form->message(__('message_4'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_3'));

        $this->admin->form->button_submit(__('button_6'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_parameter_group($product_parameter_group_id = '')
    {
        if(!$this->e_product_parameter_groups_model->item_exists($product_parameter_group_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_parameter_group();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->e_product_parameter_groups_model->set_item_data($product_parameter_group_id, $data);
            $this->admin->form->message(__('message_5'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_3'), $this->e_product_parameter_groups_model->$product_parameter_group_id->name);
        
        $this->admin->form->button_submit(__('button_7'));
        $this->admin->form->button_submit(__('button_9'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_parameter_group($product_parameter_group_id = '')
    {
        if($this->e_product_parameter_groups_model->item_exists($product_parameter_group_id))
        {
            $this->e_product_parameter_groups_model->delete_item($product_parameter_group_id);
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_parameter_group()
    {
        $this->admin->form->set_rules('name', __('field_3'), 'trim|required|max_length[255]');
    }
    
    // Parametre produktov
    
    function add_parameter($product_parameter_group_id = '')
    {
        if(!$this->e_product_parameter_groups_model->item_exists($product_parameter_group_id))
        {
            $this->admin->form->error(__('error_5'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_parameter();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['product_parameter_group_id'] = $this->input->post('product_parameter_group_id');
            
            $this->e_product_parameters_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('select', 'product_parameter_group_id', __('field_2'), $this->e_product_parameter_groups_model->get_data_in_col('name'), $product_parameter_group_id, TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_parameter($product_parameter_id = '')
    {
        if(!$this->e_product_parameters_model->item_exists($product_parameter_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_parameter();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['product_parameter_group_id'] = $this->input->post('product_parameter_group_id');
            
            $this->e_product_parameters_model->set_item_data($product_parameter_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_product_parameters_model->$product_parameter_id->_name);
        $this->admin->form->add_field('select', 'product_parameter_group_id', __('field_2'), $this->e_product_parameter_groups_model->get_data_in_col('name'), $this->e_product_parameters_model->$product_parameter_id->product_parameter_group_id, TRUE);
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_9'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_parameter($product_parameter_id = '')
    {
        if($this->e_product_parameters_model->item_exists($product_parameter_id))
        {
            $this->e_product_parameters_model->delete_item($product_parameter_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_parameter()
    {
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('product_parameter_group_id', __('field_2'), 'trim|required|item_exists_eshop[product_parameter_groups]');
    }
    
}