<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signs extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('signs');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        
        foreach($this->e_signs_model->get_data() as $sign)
        {
            switch($sign->price_impact)
            {
                case 'coef':
                    $price_impact = __('price_impact_2') . ': ' . $sign->_coef;
                    break;
                
                case 'price':
                    $price_impact = __('price_impact_3') . ': ' . parse_price($sign->_price);
                    break;
                
                default:
                    $price_impact = __('price_impact_1');
                    break;
            }
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $sign->id, $sign->_name));
            $this->admin->form->cell($price_impact);
            $this->admin->form->cell(admin_anchor('~/delete/' . $sign->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $sign->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $sign->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($sign->id, 0, $this->cms->model->eshop_table('signs'), NULL, $contextmenu);
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    function add()
    {
        if($this->input->post('price_impact') == 'coef') $this->_validation('coef');
        elseif($this->input->post('price_impact') == 'price') $this->_validation('price');
        else $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['_image'] = $this->input->post('_image');
            $data['price_impact'] = $this->input->post('price_impact');
            
            if($this->input->post('price_impact') == 'coef') $data['_coef'] = $this->input->post('_coef');
            if($this->input->post('price_impact') == 'price') $data['_price'] = $this->input->post('_price');
            
            $this->e_signs_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'));
        $this->admin->form->add_field('imagepicker', '_image', __('field_2'));
        $this->admin->form->add_field('select', 'price_impact', __('field_3'), array('' => __('price_impact_1'), 'coef' => __('price_impact_2'), 'price' => __('price_impact_3')));
        $this->admin->form->ajax_area('ajax_price_impact', array('price_impact'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function ajax_price_impact()
    {
        if(!$this->input->is_ajax_request()) show_404();

        if($this->input->post('price_impact') == 'coef') $this->_validation('coef');
        elseif($this->input->post('price_impact') == 'price') $this->_validation('price');
        else $this->_validation();
        if(form_sent()) $this->admin->form->validate();
        
        $sign_id = $this->input->post('sign_id');
        if($this->e_signs_model->item_exists($sign_id))
        {
            $value_coef = $this->e_signs_model->$sign_id->_coef;
            $value_price = $this->e_signs_model->$sign_id->_price;
        }
        else
        {
            $value_coef = 1;
            $value_price = 0;
        }
        
        $data = array();
        
        $data['result'] = TRUE;
        $data['content'] = '';
        
        switch($this->input->post('price_impact'))
        {
            case 'coef':
                $data['content'] .= $this->admin->form->get_field_row('slider', '_coef', __('field_4'), $value_coef, 0, 10, 0.01);
                break;
            
            case 'price':
                $this->admin->form->info(__('info_1'));
                $data['content'] .= $this->admin->form->get_field_row('input', '_price', __('field_5'), doubleval($value_price));
                break;
            
            case '':
                break;
            
            default:
                $data['result'] = FALSE;
                $data['error'] = __('error_3');
                break;
        }
        
        $data['field_id'] = $this->admin->form->get_field_id(TRUE);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
    function edit($sign_id = '')
    {
        if(!$this->e_signs_model->item_exists($sign_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        if($this->input->post('price_impact') == 'coef') $this->_validation('coef');
        elseif($this->input->post('price_impact') == 'price') $this->_validation('price');
        else $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['_image'] = $this->input->post('_image');
            $data['price_impact'] = $this->input->post('price_impact');
            
            if($this->input->post('price_impact') == 'coef') $data['_coef'] = $this->input->post('_coef');
            if($this->input->post('price_impact') == 'price') $data['_price'] = $this->input->post('_price');
            
            $this->e_signs_model->set_item_data($sign_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->hidden_fields['sign_id'] = $sign_id;
        
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_signs_model->$sign_id->_name);
        $this->admin->form->add_field('imagepicker', '_image', __('field_2'), $this->e_signs_model->$sign_id->_image);
        $this->admin->form->add_field('select', 'price_impact', __('field_3'), array('' => __('price_impact_1'), 'coef' => __('price_impact_2'), 'price' => __('price_impact_3')), $this->e_signs_model->$sign_id->price_impact);
        $this->admin->form->ajax_area('ajax_price_impact', array('price_impact'));
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($sign_id = '')
    {
        if($this->e_signs_model->item_exists($sign_id))
        {
            $this->e_signs_model->delete_item($sign_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($required = '')
    {
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('_image', __('field_2'), 'trim');
        $this->admin->form->set_rules('price_impact', __('field_3'), 'trim|value[coef,price]');
        $this->admin->form->set_rules('_coef', __('field_4'), 'trim|numeric|plus' . ($required == 'coef' ? '|required' : ''));
        $this->admin->form->set_rules('_price', __('field_5'), 'trim|price' . ($required == 'price' ? '|required' : ''));
    }
    
}