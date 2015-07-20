<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transport_payment extends CI_Controller {

    // TODO: dorobit "public" a ne"public" polozky
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('transports');
        $this->cms->model->load_eshop('payments');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add_transport', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        // Transports
        foreach($this->e_transports_model->get_data() as $transport)
        {
            if($transport->lang_id != lang_id()) continue;
            
            $options_cell  = admin_anchor('~/add_payment/' . $transport->id, __('button_9'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete_transport/' . $transport->id, __('button_2'), __('confirm_1'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit_transport/' . $transport->id, $transport->name));
            $this->admin->form->cell(parse_price($transport->price));
            $this->admin->form->cell($transport->price_free > 0 ? parse_price($transport->price_free) : '-');
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_9'), admin_url('~/add_payment/' . $transport->id), 'add');
            $contextmenu[] = array(__('button_3'), admin_url('~/edit_transport/' . $transport->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_transport/' . $transport->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($transport->id, 0, $this->cms->model->eshop_table('transports'), NULL, $contextmenu);
            
            // Payments
            $this->e_payments_model->where('transport_id', '=', $transport->id);
            foreach($this->e_payments_model->get_data() as $payment)
            {
                $this->admin->form->cell_left(admin_anchor('~/edit_payment/' . $payment->id, $payment->name));
                $this->admin->form->cell(parse_price($payment->price));
                $this->admin->form->cell($payment->price_free > 0 ? parse_price($payment->price_free) : '-');
                $this->admin->form->cell(admin_anchor('~/delete_payment/' . $payment->id, __('button_2'), __('confirm_2')));

                $contextmenu = array();

                $contextmenu[] = array(__('button_3'), admin_url('~/edit_payment/' . $payment->id), 'edit');
                $contextmenu[] = array(__('button_2'), admin_url('~/delete_payment/' . $payment->id), 'delete', __('confirm_2'));

                $this->admin->form->row($payment->id, 1, $this->cms->model->eshop_table('payments_' . $transport->id), TRUE, $contextmenu);
            }
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    /* Transports */
    
    function add_transport()
    {
        $this->_validation_transport();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['price'] = $this->input->post('price');
            $data['price_free'] = $this->input->post('price_free');
            
            $this->e_transports_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', 'price', __('field_2'));
        $this->admin->form->add_field('input', 'price_free', __('field_3'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_transport($transport_id = '')
    {
        if(!$this->e_transports_model->item_exists($transport_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_transport();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['price'] = $this->input->post('price');
            $data['price_free'] = $this->input->post('price_free');
            
            $this->e_transports_model->set_item_data($transport_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->hidden_fields['transport_id'] = $transport_id;
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->e_transports_model->$transport_id->name);
        $this->admin->form->add_field('input', 'price', __('field_2'), doubleval($this->e_transports_model->$transport_id->price));
        $this->admin->form->add_field('input', 'price_free', __('field_3'), ($this->e_transports_model->$transport_id->price_free > 0) ? doubleval($this->e_transports_model->$transport_id->price_free) : '');
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_transport($transport_id = '')
    {
        if($this->e_transports_model->item_exists($transport_id))
        {
            $this->e_transports_model->delete_item($transport_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_transport($required = '')
    {
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('price', __('field_2'), 'trim|price|plus|required');
        $this->admin->form->set_rules('price_free', __('field_3'), 'trim|price|plus');
    }
    
    /* Payments */
    
    function add_payment($transport_id = '')
    {
        $this->_validation_payment();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['price'] = $this->input->post('price');
            $data['price_free'] = $this->input->post('price_free');
            $data['transport_id'] = $this->input->post('transport_id');
            
            $this->e_payments_model->add_item($data);
            $this->admin->form->message(__('message_4'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_4'));
        $this->admin->form->add_field('input', 'price', __('field_5'));
        $this->admin->form->add_field('input', 'price_free', __('field_6'));
        $this->admin->form->add_field('select', 'transport_id', __('field_7'), $this->eshop->transport_payment->get_transports_select_data(), $transport_id);
        
        $this->admin->form->button_submit(__('button_7'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_payment($payment_id = '')
    {
        if(!$this->e_payments_model->item_exists($payment_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->_validation_payment();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['price'] = $this->input->post('price');
            $data['price_free'] = $this->input->post('price_free');
            $data['transport_id'] = $this->input->post('transport_id');
            
            $this->e_payments_model->set_item_data($payment_id, $data);
            $this->admin->form->message(__('message_5'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->hidden_fields['payment_id'] = $payment_id;
        
        $this->admin->form->add_field('input', 'name', __('field_4'), $this->e_payments_model->$payment_id->name);
        $this->admin->form->add_field('input', 'price', __('field_5'), doubleval($this->e_payments_model->$payment_id->price));
        $this->admin->form->add_field('input', 'price_free', __('field_6'), ($this->e_payments_model->$payment_id->price_free > 0) ? doubleval($this->e_payments_model->$payment_id->price_free) : '');
        $this->admin->form->add_field('select', 'transport_id', __('field_7'), $this->eshop->transport_payment->get_transports_select_data(), $this->e_payments_model->$payment_id->transport_id);
        
        $this->admin->form->button_submit(__('button_8'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_payment($payment_id = '')
    {
        if($this->e_payments_model->item_exists($payment_id))
        {
            $this->e_payments_model->delete_item($payment_id);
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_payment($required = '')
    {
        $this->admin->form->set_rules('name', __('field_4'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('price', __('field_5'), 'trim|price|plus|required');
        $this->admin->form->set_rules('price_free', __('field_6'), 'trim|price|plus');
        $this->admin->form->set_rules('transport_id', __('field_7'), 'trim|required|transport_id');
    }
    
}