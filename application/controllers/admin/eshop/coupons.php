<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupons extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('coupons');
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
        
        foreach($this->e_coupons_model->get_data() as $coupon)
        {
            if($coupon->time_from != '')
            {
                // Od .. do ...
                if($coupon->time_to != '') $period_cell = __('time_from') . ' <strong>' . date(cfg('time_format', 'normal_date'), $coupon->time_from) . '</strong> ' . __('time_to') . ' <strong>' . date(cfg('time_format', 'normal_date'), $coupon->time_to) . '</strong>';
                // Od ...
                else $period_cell = __('time_from') . ' <strong>' . date(cfg('time_format', 'normal_date'), $coupon->time_from) . '</strong>';
            }
            // Do ...
            elseif($coupon->time_to != '') $period_cell = __('time_to_b') . ' <strong>' . date(cfg('time_format', 'normal_date'), $coupon->time_to) . '</strong>';
            // Stále
            else $period_cell = __('time_still');
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $coupon->id, $coupon->_name));
            $this->admin->form->cell($coupon->code);
            $this->admin->form->cell($coupon->count);
            $this->admin->form->cell($coupon->discount . '%');
            $this->admin->form->cell($this->admin->form->cell_checkbox((($coupon->active) ? '~/deactive_coupon/' : '~/active_coupon/') . $coupon->id, (($coupon->active) ? __('button_7') : __('button_8')), $coupon->active));
            $this->admin->form->cell($period_cell);
            $this->admin->form->cell(admin_anchor('~/delete/' . $coupon->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $coupon->id), 'edit');
            if($coupon->active) $contextmenu[] = array(__('button_7'), admin_url('~/deactive_coupon/' . $coupon->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/active_coupon/' . $coupon->id), 'check');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $coupon->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($coupon->id, 0, $this->cms->model->eshop_table('coupons'), NULL, $contextmenu);
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        $this->admin->form->generate();
    }
    
    function deactive_coupon($coupon_id = '')
    {
        if($this->e_coupons_model->item_exists($coupon_id))
        {
            $this->e_coupons_model->set_item_data($coupon_id, array('active' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function active_coupon($coupon_id = '')
    {
        if($this->e_coupons_model->item_exists($coupon_id))
        {
            $this->e_coupons_model->set_item_data($coupon_id, array('active' => TRUE));
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
        $this->load->helper('string');
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['code'] = $this->input->post('code');
            $data['count'] = $this->input->post('count');
            $data['discount'] = $this->input->post('discount');
            $data['time_from'] = $this->input->post('time_from');
            $data['time_to'] = $this->input->post('time_to');
            $data['active'] = $this->input->post('active');
            
            $this->e_coupons_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('input', 'code', __('field_2'), strtolower(random_string('alnum', 6)));
        $this->admin->form->add_field('input', 'count', __('field_3'));
        $this->admin->form->add_field('input', 'discount', __('field_4'));
        $this->admin->form->add_field('doubledate', 'time_from', 'time_to', __('field_5'));
        $this->admin->form->add_field('checkbox', 'active', __('field_6'), TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($coupon_id = '')
    {
        if(!$this->e_coupons_model->item_exists($coupon_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->load->helper('string');
        
        $this->_validation($coupon_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['code'] = $this->input->post('code');
            $data['count'] = $this->input->post('count');
            $data['discount'] = $this->input->post('discount');
            $data['time_from'] = $this->input->post('time_from');
            $data['time_to'] = $this->input->post('time_to');
            $data['active'] = $this->input->post('active');
            
            $this->e_coupons_model->set_item_data($coupon_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_coupons_model->$coupon_id->_name);
        $this->admin->form->info(__('random') . ': <strong>' . strtolower(random_string('alnum', 6)) . '</strong>');
        $this->admin->form->add_field('input', 'code', __('field_2'), $this->e_coupons_model->$coupon_id->code);
        $this->admin->form->add_field('input', 'count', __('field_3'), $this->e_coupons_model->$coupon_id->count);
        $this->admin->form->add_field('input', 'discount', __('field_4'), doubleval($this->e_coupons_model->$coupon_id->discount));
        $this->admin->form->add_field('doubledate', 'time_from', 'time_to', __('field_5'), $this->e_coupons_model->$coupon_id->time_from, $this->e_coupons_model->$coupon_id->time_to);
        $this->admin->form->add_field('checkbox', 'active', __('field_6'), is_form_true($this->e_coupons_model->$coupon_id->active));

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($coupon_id = '')
    {
        if($this->e_coupons_model->item_exists($coupon_id))
        {
            $this->e_coupons_model->delete_item($coupon_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($coupon_id = '')
    {
        $unique_code = (!form_sent() || $this->e_coupons_model->$coupon_id->code == $this->input->post('code')) ? '' : '|unique_eshop[coupons.code]';
        
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('code', __('field_2'), 'trim|required|exact_length[6]' . $unique_code);
        $this->admin->form->set_rules('count', __('field_3'), 'trim|required|integer|plus');
        $this->admin->form->set_rules('discount', __('field_4'), 'trim|required|percent');
        $this->admin->form->set_rules('active', __('field_6'), 'trim|intval');
    }
    
}