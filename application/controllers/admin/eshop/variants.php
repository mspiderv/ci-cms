<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Variants extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('variants');
        $this->cms->model->load_eshop('variant_values');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add_variant', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        
        foreach($this->e_variants_model->get_data() as $variant)
        {
            $options_cell  = admin_anchor('~/add_variant_value/' . $variant->id, __('button_8'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete_variant/' . $variant->id, __('button_2'), __('confirm_2'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit_variant/' . $variant->id, $variant->_name));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_8'), admin_url('~/add_variant_value/' . $variant->id), 'add');
            $contextmenu[] = array(__('button_3'), admin_url('~/edit_variant/' . $variant->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_variant/' . $variant->id), 'delete', __('confirm_2'));
            
            $this->admin->form->row($variant->id, 0, $this->cms->model->eshop_table('variants'), FALSE, $contextmenu);
            
            $this->e_variant_values_model->where('variant_id', '=', $variant->id);
            foreach($this->e_variant_values_model->get_data() as $variant_value)
            {
                $this->admin->form->cell_left(admin_anchor('~/edit_variant_value/' . $variant_value->id, $variant_value->_name));
                $this->admin->form->cell(admin_anchor('~/delete_variant_value/' . $variant_value->id, __('button_2'), __('confirm_1')));

                $contextmenu = array();

                $contextmenu[] = array(__('button_3'), admin_url('~/edit_variant_value/' . $variant_value->id), 'edit');
                $contextmenu[] = array(__('button_2'), admin_url('~/delete_variant_value/' . $variant_value->id), 'delete', __('confirm_1'));

                $this->admin->form->row($variant_value->id, 1, $this->cms->model->eshop_table('variant_values_' . $variant->id), TRUE, $contextmenu);
            }
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    // Varianty
    
    function add_variant()
    {
        $this->_validation_variant();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            
            $this->e_variants_model->add_item($data);
            $this->admin->form->message(__('message_4'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_3'));

        $this->admin->form->button_submit(__('button_6'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_variant($variant_id = '')
    {
        if(!$this->e_variants_model->item_exists($variant_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_variant();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            
            $this->e_variants_model->set_item_data($variant_id, $data);
            $this->admin->form->message(__('message_5'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_3'), $this->e_variants_model->$variant_id->_name);
        
        $this->admin->form->button_submit(__('button_7'));
        $this->admin->form->button_submit(__('button_9'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_variant($variant_id = '')
    {
        if($this->e_variants_model->item_exists($variant_id))
        {
            $this->e_variants_model->delete_item($variant_id);
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_variant()
    {
        $this->admin->form->set_rules('_name', __('field_3'), 'trim|required|max_length[255]');
    }
    
    // Hodnoty variánt
    
    function add_variant_value($variant_id = '')
    {
        if(!$this->e_variants_model->item_exists($variant_id))
        {
            $this->admin->form->error(__('error_5'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_variant_value();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['variant_id'] = $this->input->post('variant_id');
            
            $this->e_variant_values_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('select', 'variant_id', __('field_2'), $this->e_variants_model->get_data_in_col('_name'), $variant_id, TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_variant_value($variant_value_id = '')
    {
        if(!$this->e_variant_values_model->item_exists($variant_value_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_variant_value();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['variant_id'] = $this->input->post('variant_id');
            
            $this->e_variant_values_model->set_item_data($variant_value_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_variant_values_model->$variant_value_id->_name);
        $this->admin->form->add_field('select', 'variant_id', __('field_2'), $this->e_variants_model->get_data_in_col('_name'), $this->e_variant_values_model->$variant_value_id->variant_id, TRUE);
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_9'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_variant_value($variant_value_id = '')
    {
        if($this->e_variant_values_model->item_exists($variant_value_id))
        {
            $this->e_variant_values_model->delete_item($variant_value_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_variant_value()
    {
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('variant_id', __('field_2'), 'trim|required|item_exists_eshop[variants]');
    }
    
}