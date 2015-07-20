<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Currencies extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('currencies');
        
        $default_currency_id = db_config('default_currency_id');
        if(!$this->e_currencies_model->item_exists($default_currency_id)) db_config('default_currency_id', $this->e_currencies_model->get_first_id());
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_6'));
        $this->admin->form->col(__('col_7'));
        
        foreach($this->e_currencies_model->get_data() as $currency)
        {
            $can_delete = (db_config('default_currency_id') != $currency->id && !$this->eshop->currencies->is_used($currency->id));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $currency->id, $currency->name));
            $this->admin->form->cell($currency->course);
            $this->admin->form->cell($currency->symbol);
            $this->admin->form->cell($currency->decimals);
            $this->admin->form->cell($currency->round);
            $this->admin->form->cell($this->admin->form->cell_radio('~/set_default_currency/' . $currency->id, __('button_7'), (db_config('default_currency_id') == $currency->id), __('confirm_1')));
            $this->admin->form->cell(($can_delete) ? admin_anchor('~/delete/' . $currency->id, __('button_2'), __('confirm_2')) : '');

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $currency->id), 'edit');
            $contextmenu[] = array(__('button_7'), admin_url('~/set_default_currency/' . $currency->id), 'check', __('confirm_1'));
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $currency->id), 'delete', __('confirm_2'));
            
            $this->admin->form->row($currency->id, 0, $this->cms->model->eshop_table('currencies'), NULL, $contextmenu);
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        $this->admin->form->generate();
    }
    
    function set_default_currency($currency_id = '')
    {
        if($this->e_currencies_model->item_exists($currency_id))
        {
            db_config('default_currency_id', $currency_id);
            $this->e_currencies_model->set_item_data($currency_id, array('course' => 1));
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
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['course'] = $this->input->post('course');
            $data['symbol'] = $this->input->post('symbol');
            $data['decimals'] = $this->input->post('decimals');
            $data['round'] = $this->input->post('round');
            
            $this->e_currencies_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', 'course', __('field_2'));
        $this->admin->form->add_field('input', 'symbol', __('field_3'));
        $this->admin->form->add_field('slider', 'decimals', __('field_4'), cfg('price', 'default_decimals'), 0, cfg('price', 'max_decimal'), 1);
        $this->admin->form->add_field('slider', 'round', __('field_5'), cfg('price', 'default_round'), -cfg('price', 'max_decimal'), cfg('price', 'max_decimal'), 1);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        $this->admin->form->button_helper(__('helper_2'), __('helper_title_1'));
        
        $this->admin->form->generate();
    }
    
    function edit($currency_id = '')
    {
        if(!$this->e_currencies_model->item_exists($currency_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation(db_config('default_currency_id') != $currency_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            if(db_config('default_currency_id') == $currency_id) $data['course'] = 1;
            else $data['course'] = $this->input->post('course');
            $data['symbol'] = $this->input->post('symbol');
            $data['decimals'] = $this->input->post('decimals');
            $data['round'] = $this->input->post('round');
            
            $this->e_currencies_model->set_item_data($currency_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->e_currencies_model->$currency_id->name);
        if(db_config('default_currency_id') != $currency_id)
        $this->admin->form->add_field('input', 'course', __('field_2'), $this->e_currencies_model->$currency_id->course);
        $this->admin->form->add_field('input', 'symbol', __('field_3'), $this->e_currencies_model->$currency_id->symbol);
        $this->admin->form->add_field('slider', 'decimals', __('field_4'), $this->e_currencies_model->$currency_id->decimals, 0, cfg('price', 'max_decimal'), 1);
        $this->admin->form->add_field('slider', 'round', __('field_5'), $this->e_currencies_model->$currency_id->round, -cfg('price', 'max_decimal'), cfg('price', 'max_decimal'), 1);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        $this->admin->form->button_helper(__('helper_2'), __('helper_title_1'));
        
        $this->admin->form->generate();
    }
    
    function delete($currency_id = '')
    {
        if($this->e_currencies_model->item_exists($currency_id))
        {
            if(db_config('default_currency_id') == $currency_id)
            {
                $this->admin->form->error(__('error_4'), TRUE);
            }
            elseif($this->eshop->currencies->is_used($currency->id))
            {
                $this->admin->form->error(__('error_5'), TRUE);
            }
            else
            {
                $this->e_currencies_model->delete_item($currency_id);
                $this->admin->form->message(__('message_3'), TRUE);
            }
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($course = TRUE)
    {
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        if($course)
        $this->admin->form->set_rules('course', __('field_2'), 'trim|required|numeric|greater_than[0]|max_length[16]');
        $this->admin->form->set_rules('symbol', __('field_3'), 'trim|required|max_length[10]');
        $this->admin->form->set_rules('decimals', __('field_4'), 'trim|required|is_natural|less_than[' . (cfg('price', 'max_decimal') + 1) . ']');
        $this->admin->form->set_rules('round', __('field_5'), 'trim|required|integer|less_than[' . (cfg('price', 'max_decimal') + 1) . ']|greater_than[' . (-(cfg('price', 'max_decimal')) - 1) . ']');
    }
    
}