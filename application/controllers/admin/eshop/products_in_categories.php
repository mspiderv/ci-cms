<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products_in_categories extends CI_Controller {
    
    // TODO: widgetom (aj itemom) spravit contextmenu ???
    // Napriklad by sa tak dali upravovat nazvy kategorii priamo z categorizing
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('products');
        $this->cms->model->load_eshop('categories');
        $this->cms->model->load_eshop('products_in_categories');
    }
    
    function index()
    {
        $this->admin->form->button_helper(__('helper_1'));
        
        // Create products
        foreach($this->e_products_model->get_data_in_col('_name') as $product_id => $product_name)
        {
            $this->admin->form->categorizing_add_item($product_id, $product_name);
        }
        
        // Create categories
        foreach($this->e_categories_model->get_data_in_col('_name') as $category_id => $category_name)
        {
            // TODO: do nazvu by asi bolo nejakym sposobom vhodne zakomponovat strukturu (nie len "jablka" ale "potraviny -> ovocie -> jablka")
            $this->admin->form->categorizing_add_widget($category_id, $category_name);
        }
        
        // Add products to categories
        foreach($this->e_products_in_categories_model->get_data() as $product_in_category)
        {
            $this->admin->form->categorizing_add_item_to_widget($product_in_category->category_id, $product_in_category->product_id);
        }
        
        $categorizing_options = array(
            'widget_sort_method' => 'update_categories', 
            'item_sort_method' => 'update',
            'unique' => FALSE,
            'unique_in_widget' => TRUE,
            'delete_item' => __('label_1')
        );
        
        $this->admin->form->categorizing($categorizing_options);
        
        $this->admin->form->generate();
    }
    
    function update_categories()
    {
        if(!$this->input->is_ajax_request()) show_404();
        
        $data = array();
        
        $table = $this->e_categories_model->get_table();
        $items = $this->e_categories_model->get_ids();
        $sort = json_decode($this->input->post('sort'));
        
        $result = $this->cms->save_sort($table, $items, $sort);
        
        if($result === TRUE)
        {
            $data['result'] = TRUE;
            $data['error'] = '';
        }
        elseif($result == 'error_1')
        {
            $data['result'] = FALSE;
            $data['error'] = __('error_4');
        }
        elseif($result == 'error_2')
        {
            $data['result'] = FALSE;
                $data['error'] = __('error_3');
        }
        else
        {
            $data['result'] = FALSE;
            $data['error'] = '';
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
    function update()
    {
        if(!$this->input->is_ajax_request()) show_404();

        $data = array();
        $data['result'] = TRUE;
        $data['error'] = '';
        
        $category_id = $this->input->post('category_id');
        $product_ids = (array)json_decode($this->input->post('sort'));
        
        if($this->e_categories_model->item_exists($category_id))
        {
            $this->e_products_in_categories_model->where('category_id', '=', $category_id);
            $this->e_products_in_categories_model->delete();

            foreach($product_ids as $product_id)
            {
                if($this->e_products_model->item_exists($product_id))
                {
                    $product_in_category_data = array();

                    $product_in_category_data['category_id'] = $category_id;
                    $product_in_category_data['product_id'] = $product_id;

                    $this->e_products_in_categories_model->add_item($product_in_category_data);
                }
                else
                {
                    $data['result'] = FALSE;
                    $data['error'] = sprintf(__('error_1'), $product_id, $category_id);
                }
            }
        }
        else
        {
            $data['result'] = FALSE;
            $data['error'] = sprintf(__('error_2'), $category_id);
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
}