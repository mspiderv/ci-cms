<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_galleries extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('product_galleries');
        $this->cms->model->load_eshop('product_gallery_images');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        
        foreach($this->e_product_galleries_model->get_data() as $product_gallery)
        {
            $this->e_product_gallery_images_model->where('product_gallery_id', '=', $product_gallery->id);
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $product_gallery->id, $product_gallery->_name));
            $this->admin->form->cell($this->e_product_gallery_images_model->get_rows());
            $this->admin->form->cell(admin_anchor('~/delete/' . $product_gallery->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $product_gallery->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $product_gallery->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($product_gallery->id, 0, $this->cms->model->eshop_table('product_galleries'), NULL, $contextmenu);
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            
            $this->e_product_galleries_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($product_gallery_id = '')
    {
        if(!$this->e_product_galleries_model->item_exists($product_gallery_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['_name'] = $this->input->post('_name');
            $data['primary_image_id'] = $this->input->post('primary_image_id');
            
            if(!$this->e_product_gallery_images_model->item_exists($data['primary_image_id'])) unset($data['primary_image_id']);
            $this->e_product_galleries_model->set_item_data($product_gallery_id, $data);
            
            // Hromadne upravy
            $this->e_product_gallery_images_model->where('product_gallery_id', '=', $product_gallery_id);
            foreach($this->e_product_gallery_images_model->get_ids() as $product_gallery_image_id)
            {
                $image_data = array();
                $image_data['_alt'] = $this->input->post('_image_alt_' . $product_gallery_image_id);
                $image_data['_title'] = $this->input->post('_image_title_' . $product_gallery_image_id);
                
                $this->e_product_gallery_images_model->set_item_data($product_gallery_image_id, $image_data);
            }
            
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', '_name', __('field_1'), $this->e_product_galleries_model->$product_gallery_id->_name);
        
        /* Gallery images */
        
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_6'));
        $this->admin->form->col(__('col_9'));
        $this->admin->form->col(__('col_10'));
        $this->admin->form->col(__('col_11'));
        $this->admin->form->col(__('col_12'));
        $this->admin->form->col(__('col_7'));
        $this->admin->form->col(__('col_8'));
        
        $primary_image_id = $this->eshop->product_galleries->get_gallery_primary_image_id($product_gallery_id);
        
        $this->load->helper('number');
        
        $this->e_product_gallery_images_model->where('product_gallery_id', '=', $product_gallery_id);
        foreach($this->e_product_gallery_images_model->get_data() as $product_gallery_image)
        {
            $filename = $_SERVER['DOCUMENT_ROOT'] . urldecode($product_gallery_image->url);
            $image_size = @getimagesize($filename);
            
            $width = intval($image_size[0]);
            $width = ($width > 0) ? $width : '?';
            
            $height = intval($image_size[1]);
            $height = ($height > 0) ? $height : '?';
            
            $size = byte_format(@filesize($filename));
            
            $format = strtoupper(get_ext($product_gallery_image->url));
            
            $this->admin->form->cell_left(admin_anchor('~/edit_image/' . $product_gallery_image->id, $this->admin->form->cell_thumbnail($this->eshop->product_galleries->get_image_filename($product_gallery_image->id), $product_gallery_image->url)));
            $this->admin->form->cell($this->admin->form->get_field_row('cell_input', '_image_alt_' . $product_gallery_image->id, __('field_3'), $product_gallery_image->_alt));
            $this->admin->form->cell($this->admin->form->get_field_row('cell_input', '_image_title_' . $product_gallery_image->id, __('field_4'), $product_gallery_image->_title));
            $this->admin->form->cell($width . ' px');
            $this->admin->form->cell($height . ' px');
            $this->admin->form->cell($size);
            $this->admin->form->cell($format);
            $this->admin->form->cell($this->admin->form->get_field_row('cell_radio', 'primary_image_id', __('button_9'), $product_gallery_image->id, ($primary_image_id == $product_gallery_image->id)));
            $this->admin->form->cell(admin_anchor('~/delete_image/' . $product_gallery_image->id, __('button_2'), __('confirm_2')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit_image/' . $product_gallery_image->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_image/' . $product_gallery_image->id), 'delete', __('confirm_2'));
            
            $this->admin->form->row($product_gallery_image->id, 0, $this->cms->model->eshop_table('product_gallery_images'), NULL, $contextmenu);
        }
        
        $this->admin->form->button_admin_link('~/add_image/' . $product_gallery_id, __('button_8'), 'plus');
        $this->admin->form->button_admin_link('~/add_multiple/' . $product_gallery_id, __('button_11'), 'image');
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        $this->admin->form->button_helper(__('helper_2'));
        
        $this->admin->form->generate();
    }
    
    function edit_image($product_gallery_image_id = '')
    {
        if(!$this->e_product_gallery_images_model->item_exists($product_gallery_image_id))
        {
            $this->admin->form->error(__('error_5'), TRUE);
            admin_redirect();
        }
        
        $product_gallery_id = $this->e_product_gallery_images_model->$product_gallery_image_id->product_gallery_id;
        
        $this->_image_validation();
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->e_product_galleries_model->$product_gallery_id->_name,
            'href' => admin_url('~/edit/' . $product_gallery_id)
        ));
                
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['url'] = $this->input->post('url');
            $data['_alt'] = $this->input->post('_alt');
            $data['_title'] = $this->input->post('_title');
            
            $this->e_product_gallery_images_model->set_item_data($product_gallery_image_id, $data);
            $this->admin->form->message(__('message_6'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect('~/edit/' . $product_gallery_id);
        }
        
        $this->admin->form->add_field('imagepicker', 'url', __('field_2'), $this->e_product_gallery_images_model->$product_gallery_image_id->url);
        $this->admin->form->add_field('input', '_alt', __('field_3'), $this->e_product_gallery_images_model->$product_gallery_image_id->_alt);
        $this->admin->form->add_field('input', '_title', __('field_4'), $this->e_product_gallery_images_model->$product_gallery_image_id->_title);
        
        $this->admin->form->button_submit(__('button_7'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_admin_link('~/edit/' . $product_gallery_id, $this->e_product_galleries_model->$product_gallery_id->_name, 'arrowreturnthick-1-w');
        
        $this->admin->form->generate_index_button = FALSE;
        $this->admin->form->generate();
    }
    
    function add_image($product_gallery_id = '')
    {
        if(!$this->e_product_galleries_model->item_exists($product_gallery_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->_image_validation();
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->e_product_galleries_model->$product_gallery_id->_name,
            'href' => admin_url('~/edit/' . $product_gallery_id)
        ));
                
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['product_gallery_id'] = $product_gallery_id;
            $data['url'] = $this->input->post('url');
            $data['_alt'] = $this->input->post('_alt');
            $data['_title'] = $this->input->post('_title');
            
            $this->e_product_gallery_images_model->add_item($data);
            
            // Try to set new image as primary
            if(intval($this->eshop->product_galleries->get_gallery_primary_image_id($product_gallery_id)) == 0)
            {
                $this->eshop->product_galleries->set_primary_image($product_gallery_id, $this->e_product_gallery_images_model->insert_id());
            }
            
            $this->admin->form->message(__('message_4'), TRUE);
            admin_redirect('~/edit/' . $product_gallery_id);
        }
        
        $this->admin->form->add_field('imagepicker', 'url', __('field_2'));
        $this->admin->form->add_field('input', '_alt', __('field_3'));
        $this->admin->form->add_field('input', '_title', __('field_4'));
        
        $this->admin->form->button_submit(__('button_7'));
        $this->admin->form->button_admin_link('~/edit/' . $product_gallery_id, $this->e_product_galleries_model->$product_gallery_id->_name, 'arrowreturnthick-1-w');
        
        $this->admin->form->generate_index_button = FALSE;
        $this->admin->form->generate();
    }
    
    function delete($product_gallery_id = '')
    {
        if($this->e_product_galleries_model->item_exists($product_gallery_id))
        {
            $this->e_product_galleries_model->delete_item($product_gallery_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function delete_image($product_gallery_image_id = '')
    {
        if($this->e_product_gallery_images_model->item_exists($product_gallery_image_id))
        {
            $product_gallery_id = $this->e_product_gallery_images_model->$product_gallery_image_id->product_gallery_id;
            
            if($this->eshop->product_galleries->image_is_primary($product_gallery_image_id))
            {
                $this->eshop->product_galleries->set_first_image_as_primary($product_gallery_id);
            }
            
            $redirect = '~/edit/' . $product_gallery_id;
            $this->e_product_gallery_images_model->delete_item($product_gallery_image_id);
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $redirect = '';
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect($redirect);
    }
    
    function add_multiple($product_gallery_id = '')
    {
        if(!$this->e_product_galleries_model->item_exists($product_gallery_id))
        {
            $this->admin->form->error(__('error_6'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->e_product_galleries_model->$product_gallery_id->_name,
            'href' => admin_url('~/edit/' . $product_gallery_id)
        ));
        
        $this->_add_multiple_validation();
        
        if($this->admin->form->validate())
        {
            $images = explode(',', $this->input->post('images'));
            
            if(is_array($images))
            {
                foreach($images as $image)
                {
                    $data = array();

                    $data['product_gallery_id'] = $product_gallery_id;
                    $data['url'] = $image;
                    
                    $image_name = cut_ext(get_filename_from_path(urldecode($image)));
                    
                    // Alt attribute
                    switch($this->input->post('alt'))
                    {
                        case 'use_name':
                            $data['_alt'] = $image_name;
                            break;
                    }
                    
                    // Tittle attribute
                    switch($this->input->post('title'))
                    {
                        case 'use_name':
                            $data['_title'] = $image_name;
                            break;
                    }
                    
                    $this->e_product_gallery_images_model->add_item($data);

                    // Try to set new image as primary
                    if(intval($this->eshop->product_galleries->get_gallery_primary_image_id($product_gallery_id)) == 0)
                    {
                        $this->eshop->product_galleries->set_primary_image($product_gallery_id, $this->e_product_gallery_images_model->insert_id());
                    }
                }
            }
            
            $this->admin->form->message(__('message_7'), TRUE);
            admin_redirect('~/edit/' . $product_gallery_id);
        }
        
        $this->admin->form->add_field('imagepicker', 'images', __('field_5'), '', TRUE);
        $this->admin->form->add_field('select', 'alt', __('field_3'), array('' => __('option_1'), 'use_name' => __('option_2')));
        $this->admin->form->add_field('select', 'title', __('field_4'), array('' => __('option_1'), 'use_name' => __('option_2')));
        
        $this->admin->form->button_submit(__('button_10'));
        $this->admin->form->button_admin_link('~/edit/' . $product_gallery_id, $this->e_product_galleries_model->$product_gallery_id->_name, 'arrowreturnthick-1-w');
        
        $this->admin->form->generate_index_button = FALSE;
        $this->admin->form->generate();
    }
    
    protected function _add_multiple_validation()
    {
        $this->admin->form->set_rules('images', __('field_5'), 'trim|required');
    }
    
    protected function _validation()
    {
        $this->admin->form->set_rules('_name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('primary_image_id');
    }
    
    protected function _image_validation()
    {
        $this->admin->form->set_rules('url', __('field_2'), 'trim|required');
        $this->admin->form->set_rules('_alt', __('field_3'), 'trim|max_length[255]');
        $this->admin->form->set_rules('_title', __('field_4'), 'trim|max_length[255]');
    }
    
}