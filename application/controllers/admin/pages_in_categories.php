<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages_in_categories extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('pages');
        $this->cms->model->load_system('categories');
        $this->cms->model->load_system('pages_in_categories');
    }
    
    function index()
    {
        $this->admin->form->button_helper(__('helper_1'));
        
        // Create pages
        foreach($this->s_pages_model->get_data_in_col('name') as $page_id => $page_name)
        {
            $this->admin->form->categorizing_add_item($page_id, $page_name);
        }
        
        // Create categories
        foreach($this->s_categories_model->get_data_in_col('_name') as $category_id => $category_name)
        {
            // TODO: do nazvu by asi bolo nejakym sposobom vhodne zakomponovat strukturu (nie len "jablka" ale "potraviny -> ovocie -> jablka")
            $this->admin->form->categorizing_add_widget($category_id, $category_name);
        }
        
        // Add pages to categories
        foreach($this->s_pages_in_categories_model->get_data() as $page_in_category)
        {
            $this->admin->form->categorizing_add_item_to_widget($page_in_category->category_id, $page_in_category->page_id);
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
        
        $table = $this->s_categories_model->get_table();
        $items = $this->s_categories_model->get_ids();
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
        $page_ids = (array)json_decode($this->input->post('sort'));
        
        if($this->s_categories_model->item_exists($category_id))
        {
            $this->s_pages_in_categories_model->where('category_id', '=', $category_id);
            $this->s_pages_in_categories_model->delete();

            foreach($page_ids as $page_id)
            {
                if($this->s_pages_model->item_exists($page_id))
                {
                    $page_in_category_data = array();

                    $page_in_category_data['category_id'] = $category_id;
                    $page_in_category_data['page_id'] = $page_id;

                    $this->s_pages_in_categories_model->add_item($page_in_category_data);
                }
                else
                {
                    $data['result'] = FALSE;
                    $data['error'] = sprintf(__('error_1'), $page_id, $category_id);
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