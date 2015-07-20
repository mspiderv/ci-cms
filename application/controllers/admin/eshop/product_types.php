<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_types extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('product_types');
        $this->cms->model->load_eshop('product_type_variables');
        $this->cms->model->load_eshop('product_type_variable_values');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        
        foreach($this->e_product_types_model->get_data() as $product_type)
        {
            $options_cell = '';
            $options_cell .= admin_anchor('~/add_variable/' . $product_type->id, __('button_5'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $product_type->id, __('button_2'), __('confirm_1'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $product_type->id, $product_type->name));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_4'), admin_url('~/edit/' . $product_type->id), 'edit');
            $contextmenu[] = array(__('button_5'), admin_url('~/add_variable/' . $product_type->id), 'add');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $product_type->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($product_type->id, 0, $this->cms->model->eshop_table('product_types'), NULL, $contextmenu);
            
            // Product type variables
            $this->e_product_type_variables_model->where('product_type_id', '=', $product_type->id);
            foreach($this->e_product_type_variables_model->get_data() as $product_type_variable)
            {
                $options_cell = '';
                $options_cell .= admin_anchor('~/add_variable_value/' . $product_type_variable->id, __('button_6'));
                $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                $options_cell .= admin_anchor('~/delete_variable/' . $product_type_variable->id, __('button_2'), __('confirm_2'));

                $this->admin->form->cell_left(admin_anchor('~/edit_variable/' . $product_type_variable->id, $product_type_variable->name));
                $this->admin->form->cell($options_cell);

                $contextmenu = array();

                $contextmenu[] = array(__('button_4'), admin_url('~/edit_variable/' . $product_type_variable->id), 'edit');
                $contextmenu[] = array(__('button_6'), admin_url('~/add_variable_value/' . $product_type_variable->id), 'add');
                $contextmenu[] = array(__('button_2'), admin_url('~/delete_variable/' . $product_type_variable->id), 'delete', __('confirm_2'));

                $this->admin->form->row($product_type_variable->id, 1, $this->cms->model->eshop_table('product_type_variables_' . $product_type->id), TRUE, $contextmenu);
                
                // Product type variable values
                $this->e_product_type_variable_values_model->where('product_type_variable_id', '=', $product_type_variable->id);
                foreach($this->e_product_type_variable_values_model->get_data() as $product_type_variable_value)
                {
                    $this->admin->form->cell_left(admin_anchor('~/edit_variable_value/' . $product_type_variable_value->id, $product_type_variable_value->_name));
                    $this->admin->form->cell(admin_anchor('~/delete_variable_value/' . $product_type_variable_value->id, __('button_2'), __('confirm_3')));

                    $contextmenu = array();

                    $contextmenu[] = array(__('button_4'), admin_url('~/edit_variable_value/' . $product_type_variable_value->id), 'edit');
                    $contextmenu[] = array(__('button_2'), admin_url('~/delete_variable_value/' . $product_type_variable_value->id), 'delete', __('confirm_3'));

                    $this->admin->form->row($product_type_variable_value->id, 2, $this->cms->model->eshop_table('product_type_variable_values_' . $product_type_variable->id), TRUE, $contextmenu);
                }
            }
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    // Typy produktov
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->e_product_types_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($product_type_id = '')
    {
        if(!$this->e_product_types_model->item_exists($product_type_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->e_product_types_model->set_item_data($product_type_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->e_product_types_model->$product_type_id->name);
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_submit(__('button_9'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($product_type_id = '')
    {
        if($this->e_product_types_model->item_exists($product_type_id))
        {
            $this->e_product_types_model->delete_item($product_type_id);
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
    
    // Premenné typy produktov
    
    function add_variable($product_type_id = '')
    {
        if(!$this->e_product_types_model->item_exists($product_type_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_variable();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['product_type_id'] = $this->input->post('product_type_id');
            
            $this->e_product_type_variables_model->add_item($data);
            $this->admin->form->message(__('message_4'), TRUE);
            
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_2'));
        $this->admin->form->add_field('select', 'product_type_id', __('field_3'), $this->e_product_types_model->get_data_in_col('name'), $product_type_id, TRUE);
        
        $this->admin->form->button_submit(__('button_7'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_variable($product_type_variable_id = '')
    {
        if(!$this->e_product_type_variables_model->item_exists($product_type_variable_id))
        {
            $this->admin->form->error(__('error_4'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_variable();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['product_type_id'] = $this->input->post('product_type_id');
            
            $this->e_product_type_variables_model->set_item_data($product_type_variable_id, $data);
            $this->admin->form->message(__('message_5'), url_param() != 'accept');
            
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_2'), $this->e_product_type_variables_model->$product_type_variable_id->name);
        $this->admin->form->add_field('select', 'product_type_id', __('field_3'), $this->e_product_types_model->get_data_in_col('name'), $this->e_product_type_variables_model->$product_type_variable_id->product_type_id, TRUE);
        
        $this->admin->form->button_submit(__('button_7'));
        $this->admin->form->button_submit(__('button_9'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_variable($product_type_variable_id = '')
    {
        if($this->e_product_type_variables_model->item_exists($product_type_variable_id))
        {
            $this->e_product_type_variables_model->delete_item($product_type_variable_id);
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_variable()
    {
        $this->admin->form->set_rules('name', __('field_2'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('product_type_id', __('field_3'), 'trim|required|item_exists_eshop[product_types]');
    }
    
    // Hodnoty premenných typov produktov
    
    function add_variable_value($product_type_variable_id = '')
    {
        if(!$this->e_product_type_variables_model->item_exists($product_type_variable_id))
        {
            $this->admin->form->error(__('error_6'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_variable_value();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['product_type_variable_id'] = $this->input->post('product_type_variable_id');
            
            $this->e_product_type_variable_values_model->add_item($data);
            $this->admin->form->message(__('message_7'), TRUE);
            
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_4'));
        $this->admin->form->add_field('select', 'product_type_variable_id', __('field_5'), $this->e_product_type_variables_model->get_data_in_col('name'), $product_type_variable_id, TRUE);
        
        $this->admin->form->button_submit(__('button_8'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_variable_value($product_type_variable_value_id = '')
    {
        if(!$this->e_product_type_variable_values_model->item_exists($product_type_variable_value_id))
        {
            $this->admin->form->error(__('error_7'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_variable_value();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['product_type_variable_id'] = $this->input->post('product_type_variable_id');
            
            $this->e_product_type_variable_values_model->set_item_data($product_type_variable_value_id, $data);
            $this->admin->form->message(__('message_8'), url_param() != 'accept');
            
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_4'), $this->e_product_type_variable_values_model->$product_type_variable_value_id->_name);
        $this->admin->form->add_field('select', 'product_type_variable_id', __('field_5'), $this->e_product_type_variables_model->get_data_in_col('name'), $this->e_product_type_variable_values_model->$product_type_variable_value_id->product_type_variable_id, TRUE);
        
        $this->admin->form->button_submit(__('button_8'));
        $this->admin->form->button_submit(__('button_9'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_variable_value($product_type_variable_value_id = '')
    {
        if($this->e_product_type_variable_values_model->item_exists($product_type_variable_value_id))
        {
            $this->e_product_type_variable_values_model->delete_item($product_type_variable_value_id);
            $this->admin->form->message(__('message_9'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_8'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_variable_value()
    {
        $this->admin->form->set_rules('_name', __('field_4'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('product_type_variable_id', __('field_5'), 'trim|required|item_exists_eshop[product_type_variables]');
    }
    
}