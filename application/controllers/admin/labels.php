<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Labels extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('labels');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        
        foreach($this->s_labels_model->get_data() as $label)
        {
            $href = get_href($label->href);
            $id = @$href['value'];
            $href_value = '';
            
            switch(@$href['type'])
            {
                case 'page':
                    $this->cms->model->load_system('pages');
                    if($this->s_pages_model->item_exists($id))
                        $href_value = admin_anchor('pages/edit/' . $id, $this->s_pages_model->get_item_data($id, 'name'));
                    break;
                    
                case 'product':
                    $this->cms->model->load_eshop('products');
                    if($this->e_products_model->item_exists($id))
                        $href_value = admin_anchor('products/edit/' . $id, $this->e_products_model->get_item_data($id, '_name'));
                    break;
                    
                case 'category':
                    $this->cms->model->load_eshop('categories');
                    if($this->e_categories_model->item_exists($id))
                        $href_value = admin_anchor('categorys/edit/' . $id, $this->e_categories_model->get_item_data($id, '_name'));
                    break;
                    
                case 'service':
                    $this->cms->model->load_system('services');
                    if($this->s_services_model->item_exists($id))
                        $href_value = admin_anchor('services/edit/' . $id, $this->s_services_model->get_item_data($id, 'name'));
                    break;
                    
                case 'url':
                    if($id != '') $href_value = '<a href="' . $id . '">' . $id . '</a>';
                    break;
            }
            
            if($href_value != '') $href_value = ': ' . $href_value;
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $label->id, $label->code));
            $this->admin->form->cell_left('<strong>' . ll('field_href_' . @$href['type']) . '</strong>' . $href_value);
            $this->admin->form->cell(admin_anchor('~/delete/' . $label->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $label->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $label->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($label->id, 0, $this->cms->model->system_table('labels'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function add()
    {
        $this->_validation(TRUE);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['code'] = $this->input->post('code');
            $data['href'] = $this->input->post('href');
            
            $this->s_labels_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'code', __('field_1'));
        $this->admin->form->add_field('href', 'href', __('field_2'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($label_id = '')
    {
        if(!$this->s_labels_model->item_exists($label_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation($this->s_labels_model->$label_id->code != $this->input->post('code'));
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['code'] = $this->input->post('code');
            $data['href'] = $this->input->post('href');
            
            $this->s_labels_model->set_item_data($label_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'code', __('field_1'), $this->s_labels_model->$label_id->code);
        $this->admin->form->add_field('href', 'href', __('field_2'), $this->s_labels_model->$label_id->href);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($label_id = '')
    {
        if($this->s_labels_model->item_exists($label_id))
        {
            $this->s_labels_model->delete_item($label_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($unique_code = TRUE)
    {
        $unique_code = $unique_code ? '|unique_system[labels.code]' : '';
        
        $this->admin->form->set_rules('code', __('field_1'), 'trim|url_title|max_length[50]' . $unique_code);
        $this->admin->form->set_rules('href', __('field_2'), 'trim|href|required_href');
    }
    
}