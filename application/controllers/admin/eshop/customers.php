<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('customers');
        $this->cms->model->load_eshop('customer_groups');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add#tab-1', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_6'));
        $this->admin->form->col(__('col_7'));
        
        foreach($this->e_customers_model->get_data() as $customer)
        {
            $this->admin->form->cell(admin_anchor('~/edit/' . $customer->id, $customer->name));
            $this->admin->form->cell($customer->surname);
            $this->admin->form->cell($customer->email);
            $this->admin->form->cell($customer->telephone); // TODO: spravny format cisla
            $this->admin->form->cell(admin_anchor('eshop/customer_groups/edit/' . $customer->customer_group_id, $this->e_customer_groups_model->get_item_data($customer->customer_group_id, '_name')));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($customer->active) ? '~/deactive_customer/' : '~/active_customer/') . $customer->id, (($customer->active) ? __('button_7') : __('button_8')), $customer->active));
            $this->admin->form->cell(admin_anchor('~/delete/' . $customer->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $customer->id), 'edit');
            if($customer->active) $contextmenu[] = array(__('button_10'), admin_url('~/deactive_customer/' . $customer->id), 'x');
            else $contextmenu[] = array(__('button_9'), admin_url('~/active_customer/' . $customer->id), 'check');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $customer->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($customer->id, NULL, NULL, NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function deactive_customer($customer_id = '')
    {
        if($this->e_customers_model->item_exists($customer_id))
        {
            $this->e_customers_model->set_item_data($customer_id, array('active' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function active_customer($customer_id = '')
    {
        if($this->e_customers_model->item_exists($customer_id))
        {
            $this->e_customers_model->set_item_data($customer_id, array('active' => TRUE));
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
        $this->_validation(TRUE);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['surname'] = $this->input->post('surname');
            $data['email'] = $this->input->post('email');
            $data['telephone'] = $this->input->post('telephone');
            $data['customer_group_id'] = $this->input->post('customer_group_id');
            $data['password'] = $this->input->post('password');
            $data['city'] = $this->input->post('city');
            $data['street'] = $this->input->post('street');
            $data['psc'] = $this->input->post('psc');
            $data['company'] = $this->input->post('company');
            $data['ico'] = $this->input->post('ico');
            $data['dic'] = $this->input->post('dic');
            $data['active'] = is_form_true($this->input->post('active'));
            
            if(is_form_true($this->input->post('send_mail')))
            {
                // TODO: dorobit odosielanie mailov
            }
            
            $this->e_customers_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', 'surname', __('field_2'));
        $this->admin->form->add_field('input', 'email', __('field_3'));
        $this->admin->form->add_field('input', 'telephone', __('field_4'));
        $this->admin->form->add_field('select', 'customer_group_id', __('field_5'), $this->e_customer_groups_model->get_data_in_col('_name'), db_config('default_customer_group_id'));
        $this->admin->form->info(__('info_3'));
        $this->admin->form->add_field('checkbox', 'send_mail', __('field_15'), TRUE);

        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', 'password', __('field_6'));
        $this->admin->form->add_field('input', 'password_2', __('field_7'));

        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('input', 'city', __('field_8'));
        $this->admin->form->add_field('input', 'street', __('field_9'));
        $this->admin->form->add_field('input', 'psc', __('field_10'));

        $this->admin->form->tab(__('tab_4'));
        $this->admin->form->add_field('input', 'company', __('field_11'));
        $this->admin->form->add_field('input', 'ico', __('field_12'));
        $this->admin->form->add_field('input', 'dic', __('field_13'));

        $this->admin->form->tab(__('tab_5'));
        $this->admin->form->add_field('checkbox', 'active', __('field_14'), TRUE);
        
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($customer_id = '')
    {
        if(!$this->e_customers_model->item_exists($customer_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['surname'] = $this->input->post('surname');
            $data['email'] = $this->input->post('email');
            $data['telephone'] = $this->input->post('telephone');
            $data['customer_group_id'] = $this->input->post('customer_group_id');
            $data['password'] = $this->input->post('password');
            $data['city'] = $this->input->post('city');
            $data['street'] = $this->input->post('street');
            $data['psc'] = $this->input->post('psc');
            $data['company'] = $this->input->post('company');
            $data['ico'] = $this->input->post('ico');
            $data['dic'] = $this->input->post('dic');
            $data['active'] = is_form_true($this->input->post('active'));
            
            $this->e_customers_model->set_item_data($customer_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->e_customers_model->$customer_id->name);
        $this->admin->form->add_field('input', 'surname', __('field_2'), $this->e_customers_model->$customer_id->surname);
        $this->admin->form->add_field('input', 'email', __('field_3'), $this->e_customers_model->$customer_id->email);
        $this->admin->form->add_field('input', 'telephone', __('field_4'), $this->e_customers_model->$customer_id->telephone);
        $this->admin->form->add_field('select', 'customer_group_id', __('field_5'), $this->e_customer_groups_model->get_data_in_col('_name'), $this->e_customers_model->$customer_id->customer_group_id);

        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('input', 'password', __('field_6'));
        $this->admin->form->info(__('info_2'));
        $this->admin->form->add_field('input', 'password_2', __('field_7'));

        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('input', 'city', __('field_8'), $this->e_customers_model->$customer_id->city);
        $this->admin->form->add_field('input', 'street', __('field_9'), $this->e_customers_model->$customer_id->street);
        $this->admin->form->add_field('input', 'psc', __('field_10'), $this->e_customers_model->$customer_id->psc);

        $this->admin->form->tab(__('tab_4'));
        $this->admin->form->add_field('input', 'company', __('field_11'), $this->e_customers_model->$customer_id->company);
        $this->admin->form->add_field('input', 'ico', __('field_12'), $this->e_customers_model->$customer_id->ico);
        $this->admin->form->add_field('input', 'dic', __('field_13'), $this->e_customers_model->$customer_id->dic);

        $this->admin->form->tab(__('tab_5'));
        $this->admin->form->add_field('checkbox', 'active', __('field_14'), $this->e_customers_model->$customer_id->active);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($customer_id = '')
    {
        if($this->e_customers_model->item_exists($customer_id))
        {
            $this->e_customers_model->delete_item($customer_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($password_required = FALSE)
    {
        $password_required = ($password_required) ? '|required' : '';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('surname', __('field_2'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('email', __('field_3'), 'trim|required|valid_email|max_length[50]');
        $this->admin->form->set_rules('telephone', __('field_4'), 'trim|required|telephone|max_length[20]');
        $this->admin->form->set_rules('customer_group_id', __('field_5'), 'trim|required|item_exists_eshop[customer_groups]');
        $this->admin->form->set_rules('password', __('field_6'), 'trim' . $password_required . '|min_length[4]');
        $this->admin->form->set_rules('password_2', __('field_7'), 'trim' . $password_required . '|matches[password]');
        $this->admin->form->set_rules('city', __('field_8'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('street', __('field_9'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('psc', __('field_10'), 'trim|required|psc');
        $this->admin->form->set_rules('company', __('field_11'), 'trim|max_length[255]');
        $this->admin->form->set_rules('ico', __('field_12'), 'trim|ico');
        $this->admin->form->set_rules('dic', __('field_13'), 'trim|max_length[50]');
        $this->admin->form->set_rules('active', __('field_14'), 'trim|intval');
    }
    
}