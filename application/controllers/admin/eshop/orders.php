<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Zoznam premenných
time
order_state_id
transport_name
transport_price
payment_name
payment_price
communication
coupon_name
coupon_discount
no_invoice
message
currency_name
currency_course
currency_symbol
currency_decimals
currency_round
name
surname
email
telephone
city
street
psc
company
ico
dic
*/

class Orders extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('orders');
        $this->cms->model->load_eshop('order_states');
        $this->cms->model->load_eshop('order_data');
    }

    // TODO: Spravne to nezoraduje vypisy
    
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
        $this->admin->form->col(__('col_8'));
        $this->admin->form->col(__('col_9'));
        $this->admin->form->col(__('col_9_1'));
        $this->admin->form->col(__('col_10'));
        
        foreach($this->e_orders_model->get_data() as $order)
        {
            $options_cell = '';
            $options_cell .= admin_anchor('~/delete/' . $order->id, __('button_2'), __('confirm_1'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/add_data/' . $order->id, __('button_7'));
            
            $this->admin->form->cell(admin_anchor('~/edit/' . $order->id, $order->id));
            $this->admin->form->cell($this->e_order_states_model->get_item_data($order->order_state_id, 'name'));
            $this->admin->form->cell($order->name);
            $this->admin->form->cell($order->surname);
            $this->admin->form->cell($order->email);
            $this->admin->form->cell($order->telephone);
            $this->admin->form->cell($order->transport_name);
            $this->admin->form->cell($order->payment_name);
            $this->admin->form->cell($this->eshop->orders->get_order_price($order->id, FALSE, TRUE, TRUE, TRUE, TRUE));
            $this->admin->form->cell($this->eshop->orders->get_order_price($order->id, TRUE, TRUE, TRUE, TRUE, TRUE));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $order->id), 'edit');
            $contextmenu[] = array(__('button_7'), admin_url('~/add_data/' . $order->id), 'add');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $order->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($order->id, NULL, NULL, NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function add()
    {
        $this->_validation();
        
        $this->admin->form->warning(__('warning_1'));
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['time'] = time();
            $data['order_state_id'] = $this->input->post('order_state_id');
            $data['transport_name'] = $this->input->post('transport_name');
            $data['transport_price'] = $this->input->post('transport_price');
            $data['payment_name'] = $this->input->post('payment_name');
            $data['payment_price'] = $this->input->post('payment_price');
            $data['communication'] = $this->input->post('communication');
            $data['coupon_name'] = $this->input->post('coupon_name');
            $data['coupon_discount'] = $this->input->post('coupon_discount');
            $data['no_invoice'] = $this->input->post('no_invoice');
            $data['message'] = $this->input->post('message');
            $data['currency_name'] = $this->input->post('currency_name');
            $data['currency_course'] = $this->input->post('currency_course');
            $data['currency_symbol'] = $this->input->post('currency_symbol');
            $data['currency_decimals'] = $this->input->post('currency_decimals');
            $data['currency_round'] = $this->input->post('currency_round');
            $data['name'] = $this->input->post('name');
            $data['surname'] = $this->input->post('surname');
            $data['email'] = $this->input->post('email');
            $data['telephone'] = $this->input->post('telephone');
            $data['city'] = $this->input->post('city');
            $data['street'] = $this->input->post('street');
            $data['psc'] = $this->input->post('psc');
            $data['company'] = $this->input->post('company');
            $data['ico'] = $this->input->post('ico');
            $data['dic'] = $this->input->post('dic');
            
            $this->e_orders_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('select', 'order_state_id', __('field_1'), $this->e_order_states_model->get_data_in_col('name'), db_config('default_order_state_id'));
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('checkbox', 'send_mail', __('field_26'), TRUE);
        $this->admin->form->add_field('input', 'communication', __('field_3'));
        $this->admin->form->add_field('checkbox', 'no_invoice', __('field_4'));
        $this->admin->form->add_field('textarea', 'message', __('field_5'));
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', 'currency_name', __('field_6'));
        $this->admin->form->add_field('input', 'currency_course', __('field_7'));
        $this->admin->form->add_field('input', 'currency_symbol', __('field_8'));
        $this->admin->form->add_field('slider', 'currency_decimals', __('field_9'), cfg('price', 'default_decimals'), 0, cfg('price', 'max_decimal'), 1);
        $this->admin->form->add_field('slider', 'currency_round', __('field_27'), cfg('price', 'default_round'), -cfg('price', 'max_decimal'), cfg('price', 'max_decimal'), 1);
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('input', 'transport_name', __('field_10'));
        $this->admin->form->add_field('input', 'transport_price', __('field_11'));
        $this->admin->form->add_field('input', 'payment_name', __('field_12'));
        $this->admin->form->add_field('input', 'payment_price', __('field_13'));
        $this->admin->form->add_field('input', 'coupon_name', __('field_14'));
        $this->admin->form->add_field('input', 'coupon_discount', __('field_15'));
        
        $this->admin->form->tab(__('tab_4'));
        $this->admin->form->add_field('input', 'name', __('field_16'));
        $this->admin->form->add_field('input', 'surname', __('field_17'));
        $this->admin->form->add_field('input', 'email', __('field_18'));
        $this->admin->form->add_field('input', 'telephone', __('field_19'));
        $this->admin->form->add_field('input', 'city', __('field_20'));
        $this->admin->form->add_field('input', 'street', __('field_21'));
        $this->admin->form->add_field('input', 'psc', __('field_22'));
        
        $this->admin->form->tab(__('tab_5'));
        $this->admin->form->add_field('input', 'company', __('field_23'));
        $this->admin->form->add_field('input', 'ico', __('field_24'));
        $this->admin->form->add_field('input', 'dic', __('field_25'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        $this->admin->form->button_helper(__('helper_1'), __('helper_title_1'));
        
        $this->admin->form->generate();
    }
    
    function edit($order_id = '')
    {
        if(!$this->e_orders_model->item_exists($order_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['order_state_id'] = $this->input->post('order_state_id');
            $data['transport_name'] = $this->input->post('transport_name');
            $data['transport_price'] = $this->input->post('transport_price');
            $data['payment_name'] = $this->input->post('payment_name');
            $data['payment_price'] = $this->input->post('payment_price');
            $data['communication'] = $this->input->post('communication');
            $data['coupon_name'] = $this->input->post('coupon_name');
            $data['coupon_discount'] = $this->input->post('coupon_discount');
            $data['no_invoice'] = $this->input->post('no_invoice');
            $data['message'] = $this->input->post('message');
            $data['currency_name'] = $this->input->post('currency_name');
            $data['currency_course'] = $this->input->post('currency_course');
            $data['currency_symbol'] = $this->input->post('currency_symbol');
            $data['currency_decimals'] = $this->input->post('currency_decimals');
            $data['currency_round'] = $this->input->post('currency_round');
            $data['name'] = $this->input->post('name');
            $data['surname'] = $this->input->post('surname');
            $data['email'] = $this->input->post('email');
            $data['telephone'] = $this->input->post('telephone');
            $data['city'] = $this->input->post('city');
            $data['street'] = $this->input->post('street');
            $data['psc'] = $this->input->post('psc');
            $data['company'] = $this->input->post('company');
            $data['ico'] = $this->input->post('ico');
            $data['dic'] = $this->input->post('dic');
            
            $this->e_orders_model->set_item_data($order_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('select', 'order_state_id', __('field_1'), $this->e_order_states_model->get_data_in_col('name'), $this->e_orders_model->$order_id->order_state_id);
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('checkbox', 'send_mail', __('field_26'), TRUE);
        $this->admin->form->add_field('input', 'communication', __('field_3'), $this->e_orders_model->$order_id->communication);
        $this->admin->form->add_field('checkbox', 'no_invoice', __('field_4'), $this->e_orders_model->$order_id->no_invoice);
        $this->admin->form->add_field('textarea', 'message', __('field_5'), $this->e_orders_model->$order_id->message);
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', 'currency_name', __('field_6'), $this->e_orders_model->$order_id->currency_name);
        $this->admin->form->add_field('input', 'currency_course', __('field_7'), $this->e_orders_model->$order_id->currency_course);
        $this->admin->form->add_field('input', 'currency_symbol', __('field_8'), $this->e_orders_model->$order_id->currency_symbol);
        $this->admin->form->add_field('slider', 'currency_decimals', __('field_9'), $this->e_orders_model->$order_id->currency_decimals, 0, cfg('price', 'max_decimal'), 1);
        $this->admin->form->add_field('slider', 'currency_round', __('field_27'), $this->e_orders_model->$order_id->currency_round, -cfg('price', 'max_decimal'), cfg('price', 'max_decimal'), 1);
        
        $this->admin->form->tab(__('tab_3'));
        $coupon_discount = doubleval($this->e_orders_model->$order_id->coupon_discount);
        $this->admin->form->add_field('input', 'transport_name', __('field_10'), $this->e_orders_model->$order_id->transport_name);
        $this->admin->form->add_field('input', 'transport_price', __('field_11'), doubleval($this->e_orders_model->$order_id->transport_price));
        $this->admin->form->add_field('input', 'payment_name', __('field_12'), $this->e_orders_model->$order_id->payment_name);
        $this->admin->form->add_field('input', 'payment_price', __('field_13'), doubleval($this->e_orders_model->$order_id->payment_price));
        $this->admin->form->add_field('input', 'coupon_name', __('field_14'), $this->e_orders_model->$order_id->coupon_name);
        $this->admin->form->add_field('input', 'coupon_discount', __('field_15'), ($coupon_discount > 0 ? $coupon_discount : ''));
        
        $this->admin->form->tab(__('tab_4'));
        $this->admin->form->add_field('input', 'name', __('field_16'), $this->e_orders_model->$order_id->name);
        $this->admin->form->add_field('input', 'surname', __('field_17'), $this->e_orders_model->$order_id->surname);
        $this->admin->form->add_field('input', 'email', __('field_18'), $this->e_orders_model->$order_id->email);
        $this->admin->form->add_field('input', 'telephone', __('field_19'), $this->e_orders_model->$order_id->telephone);
        $this->admin->form->add_field('input', 'city', __('field_20'), $this->e_orders_model->$order_id->city);
        $this->admin->form->add_field('input', 'street', __('field_21'), $this->e_orders_model->$order_id->street);
        $this->admin->form->add_field('input', 'psc', __('field_22'), $this->e_orders_model->$order_id->psc);
        
        $this->admin->form->tab(__('tab_5'));
        $this->admin->form->add_field('input', 'company', __('field_23'), $this->e_orders_model->$order_id->company);
        $this->admin->form->add_field('input', 'ico', __('field_24'), $this->e_orders_model->$order_id->ico);
        $this->admin->form->add_field('input', 'dic', __('field_25'), $this->e_orders_model->$order_id->dic);
        
        $this->admin->form->tab(__('tab_6'));
        
        $this->admin->form->col(__('col_11'));
        $this->admin->form->col(__('col_12'));
        $this->admin->form->col(__('col_13'));
        $this->admin->form->col(__('col_14'));
        $this->admin->form->col(__('col_15'));
        $this->admin->form->col(__('col_16'));
        $this->admin->form->col(__('col_17'));
        $this->admin->form->col(__('col_18'));
        $this->admin->form->col(__('col_19'));
        
        $this->e_order_data_model->where('order_id', '=', $order_id);
        foreach($this->e_order_data_model->get_data() as $order_data)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit_data/' . $order_data->id, $order_data->name));
            $this->admin->form->cell(parse_price($order_data->price, $this->e_orders_model->$order_id->currency_decimals, $this->e_orders_model->$order_id->currency_round, TRUE, $this->e_orders_model->$order_id->currency_symbol));
            $this->admin->form->cell($order_data->quantity);
            $this->admin->form->cell($order_data->sku);
            $this->admin->form->cell($order_data->ean);
            $this->admin->form->cell($order_data->tax . '%');
            $this->admin->form->cell($order_data->manufacturer);
            $this->admin->form->cell($order_data->distributor);
            $this->admin->form->cell(admin_anchor('~/delete_data/' . $order_data->id, __('button_2'), __('confirm_2')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit_data/' . $order_data->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_data/' . $order_data->id), 'delete', __('confirm_2'));
            
            $this->admin->form->row($order_data->id, 0, $this->cms->model->eshop_table('order_data'), NULL, $contextmenu);
        }
        
        $this->admin->form->listing();
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_admin_link('~/add_data/' . $order_id, __('button_7'), 'plus');
        $this->admin->form->button_index();
        $this->admin->form->button_helper(__('helper_1'), __('helper_title_1'));
        
        $this->admin->form->generate();
    }
    
    function add_data($order_id = '')
    {
        /*
         * premenne dat v objednavke
         * 
         * id - smallint 5 unsigned
         * order_id - int 10 unsigned
         * name - varchar 255
         * price - decimal(12,2) 0.00
         * quantity - smallint 5
         * sku - varchar 50 null
         * ean - varchar 50 null
         * tax - decimal (5,2)
         * manufacturer - varchar 255 null
         * distributor - varchar 255 null
         */
        
        if(!$this->e_orders_model->item_exists($order_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->_order_data_validation(TRUE);
        
        $this->admin->form->add_breadcrumb(array(
            'text' => __('breadcrumb_1') . ' ' . $order_id,
            'href' => admin_url('~/edit/' . $order_id . '#tab-6')
        ));
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['order_id'] = $order_id;
            $data['name'] = $this->input->post('name');
            $data['price'] = $this->input->post('price');
            $data['quantity'] = $this->input->post('quantity');
            $data['sku'] = $this->input->post('sku');
            $data['ean'] = $this->input->post('ean');
            $data['tax'] = $this->input->post('tax');
            $data['manufacturer'] = $this->input->post('manufacturer');
            $data['distributor'] = $this->input->post('distributor');

            $this->e_order_data_model->add_item($data);
            $this->admin->form->message(__('message_4'), TRUE);
            admin_redirect('~/edit/' . $order_id . '#tab-6');
        }
        
        $this->admin->form->add_field('input', 'name', __('field_31'));
        $this->admin->form->add_field('input', 'price', __('field_32'));
        $this->admin->form->add_field('input', 'quantity', __('field_33'));
        $this->admin->form->add_field('input', 'sku', __('field_34'));
        $this->admin->form->add_field('input', 'ean', __('field_35'));
        $this->admin->form->add_field('input', 'tax', __('field_36'));
        $this->admin->form->add_field('input', 'manufacturer', __('field_37'));
        $this->admin->form->add_field('input', 'distributor', __('field_38'));
        
        $this->admin->form->button_submit(__('button_8'));
        $this->admin->form->button_admin_link('~/edit/' . $order_id . '#tab-6', __('breadcrumb_1'), 'arrowreturnthick-1-w');
        
        $this->admin->form->generate_index_button = FALSE;
        $this->admin->form->generate();
    }
    
    function edit_data($order_data_id = '')
    {
        if(!$this->e_order_data_model->item_exists($order_data_id))
        {
            $this->admin->form->error(__('error_4'), TRUE);
            admin_redirect();
        }
        
        $this->_order_data_validation(TRUE);
        
        $order_id = $this->e_order_data_model->$order_data_id->order_id;
        
        $this->admin->form->add_breadcrumb(array(
            'text' => __('breadcrumb_1') . ' ' . $order_id,
            'href' => admin_url('~/edit/' . $order_id . '#tab-6')
        ));
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['price'] = $this->input->post('price');
            $data['quantity'] = $this->input->post('quantity');
            $data['sku'] = $this->input->post('sku');
            $data['ean'] = $this->input->post('ean');
            $data['tax'] = $this->input->post('tax');
            $data['manufacturer'] = $this->input->post('manufacturer');
            $data['distributor'] = $this->input->post('distributor');

            $this->e_order_data_model->set_item_data($order_data_id, $data);
            $this->admin->form->message(__('message_5'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect('~/edit/' . $order_id . '#tab-6');
        }
        
        $this->admin->form->add_field('input', 'name', __('field_31'), $this->e_order_data_model->$order_data_id->name);
        $this->admin->form->add_field('input', 'price', __('field_32'), doubleval($this->e_order_data_model->$order_data_id->price));
        $this->admin->form->add_field('input', 'quantity', __('field_33'), $this->e_order_data_model->$order_data_id->quantity);
        $this->admin->form->add_field('input', 'sku', __('field_34'), $this->e_order_data_model->$order_data_id->sku);
        $this->admin->form->add_field('input', 'ean', __('field_35'), $this->e_order_data_model->$order_data_id->ean);
        $this->admin->form->add_field('input', 'tax', __('field_36'), $this->e_order_data_model->$order_data_id->tax);
        $this->admin->form->add_field('input', 'manufacturer', __('field_37'), $this->e_order_data_model->$order_data_id->manufacturer);
        $this->admin->form->add_field('input', 'distributor', __('field_38'), $this->e_order_data_model->$order_data_id->distributor);
        
        $this->admin->form->button_submit(__('button_8'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_admin_link('~/edit/' . $order_id . '#tab-6', __('breadcrumb_1'), 'arrowreturnthick-1-w');
        
        $this->admin->form->generate_index_button = FALSE;
        $this->admin->form->generate();
    }
    
    function delete_data($order_data_id = '')
    {
        $redirect = '';
        
        if($this->e_order_data_model->item_exists($order_data_id))
        {
            $redirect = '~/edit/' . $this->e_order_data_model->$order_data_id->order_id . '#tab-6';
            $this->e_order_data_model->delete_item($order_data_id);
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        
        admin_redirect($redirect);
    }
    
    function delete($order_id = '')
    {
        if($this->e_orders_model->item_exists($order_id))
        {
            $this->e_orders_model->delete_item($order_id);
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
        $coupon_required = (strlen($this->input->post('coupon_name')) > 0 || doubleval($this->input->post('coupon_discount')) > 0) ? '|required' : '';
        $company_required = (strlen($this->input->post('company')) > 0 || strlen($this->input->post('ico')) > 0 || strlen($this->input->post('dic')) > 0) ? '|required' : '';
        
        $this->admin->form->set_rules('order_state_id', __('field_1'), 'trim|required|item_exists_eshop[order_states]');
        $this->admin->form->set_rules('communication', __('field_3'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('no_invoice', __('field_4'), 'trim|intval');
        $this->admin->form->set_rules('message', __('field_5'), 'trim');
        $this->admin->form->set_rules('currency_name', __('field_6'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('currency_course', __('field_7'), 'trim|required|numeric|greater_than[0]|max_length[16]');
        $this->admin->form->set_rules('currency_symbol', __('field_8'), 'trim|required|max_length[10]');
        $this->admin->form->set_rules('currency_decimals', __('field_9'), 'trim|required|is_natural|less_than[' . (cfg('price', 'max_decimal') + 1) . ']');
        $this->admin->form->set_rules('currency_round', __('field_27'), 'trim|required|integer|less_than[' . (cfg('price', 'max_decimal') + 1) . ']|greater_than[' . (-(cfg('price', 'max_decimal')) - 1) . ']');
        $this->admin->form->set_rules('transport_name', __('field_10'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('transport_price', __('field_11'), 'trim|required|price|plus');
        $this->admin->form->set_rules('payment_name', __('field_12'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('payment_price', __('field_13'), 'trim|required|price|plus');
        $this->admin->form->set_rules('coupon_name', __('field_14'), 'trim|max_length[255]' . $coupon_required);
        $this->admin->form->set_rules('coupon_discount', __('field_15'), 'trim|percent' . $coupon_required);
        $this->admin->form->set_rules('name', __('field_16'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('surname', __('field_17'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('email', __('field_18'), 'trim|required|valid_email|max_length[50]');
        $this->admin->form->set_rules('telephone', __('field_19'), 'trim|required|valid_telephone|max_length[20]');
        $this->admin->form->set_rules('city', __('field_20'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('street', __('field_21'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('psc', __('field_22'), 'trim|required|psc');
        $this->admin->form->set_rules('company', __('field_23'), 'trim|max_length[255]' . $company_required);
        $this->admin->form->set_rules('ico', __('field_24'), 'trim|ico' . $company_required);
        $this->admin->form->set_rules('dic', __('field_25'), 'trim|max_length[50]' . $company_required);
    }
    
    protected function _order_data_validation()
    {
        $this->admin->form->set_rules('name', __('field_31'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('price', __('field_32'), 'trim|price|plus|required');
        $this->admin->form->set_rules('quantity', __('field_33'), 'trim|is_natural|required|max_length[255]');
        $this->admin->form->set_rules('sku', __('field_34'), 'trim|max_length[50]');
        $this->admin->form->set_rules('ean', __('field_35'), 'trim|max_length[50]');
        $this->admin->form->set_rules('tax', __('field_36'), 'trim|required|tax|plus');
        $this->admin->form->set_rules('manufacturer', __('field_37'), 'trim|max_length[255]');
        $this->admin->form->set_rules('distributor', __('field_38'), 'trim|max_length[255]');
    }
    
}