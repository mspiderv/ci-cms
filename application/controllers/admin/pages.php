<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('pages');
        $this->cms->model->load_system('page_types');
        $this->cms->model->load_system('categories');
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
        
        foreach($this->cms->pages->get_pages_structure() as $page)
        {
            $page_id = @$page['id'];
            $page_level = @$page['level'];
            $page = $this->s_pages_model->get_item($page_id);
            $page_type_id = $page->page_type_id;
            
            $options_cell = '';
            $options_cell .= '<a href="' . href_page($page->id) . '">' . __('button_13') . '</a>';
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/duplicate/' . $page->id, __('button_9'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $page->id, __('button_2'), __('confirm_1'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/export/' . $page->id, __('button_10'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $page->id, $page->name));
            $this->admin->form->cell(admin_anchor('page_types/edit/' . $page_type_id, $this->s_page_types_model->$page_type_id->name));
            $this->admin->form->cell($page->_alias);
            $this->admin->form->cell($this->admin->form->cell_checkbox((($page->index) ? '~/unindex_page/' : '~/index_page/') . $page->id, (($page->index) ? __('button_11') : __('button_12')), $page->index));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($page->public) ? '~/unpublish_page/' : '~/publish_page/') . $page->id, (($page->public) ? __('button_7') : __('button_8')), $page->public));
            $this->admin->form->cell($this->admin->form->cell_indicator($page->sitemap_priority, 1));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $page->id), 'edit');
            $contextmenu[] = array(__('button_13'), href_page($page->id), 'show');
            
            if($page->index) $contextmenu[] = array(__('button_11'), admin_url('~/unindex_page/' . $page->id), 'noindex');
            else $contextmenu[] = array(__('button_12'), admin_url('~/index_page/' . $page->id), 'index');
            
            if($page->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_page/' . $page->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/publish_page/' . $page->id), 'check');
            
            $contextmenu[] = array(__('button_9'), admin_url('~/duplicate/' . $page->id), 'copy');
            $contextmenu[] = array(__('button_10'), admin_url('~/export/' . $page->id), 'export');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $page->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($page->id, $page_level, $this->cms->model->system_table('pages') . '_' . intval($page->parent_page_id), TRUE, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function unindex_page($page_id = '')
    {
        if($this->s_pages_model->item_exists($page_id))
        {
            $this->s_pages_model->set_item_data($page_id, array('index' => FALSE));
            $this->admin->form->message(__('message_7'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_7'), TRUE);
        }
        admin_redirect();
    }
    
    function index_page($page_id = '')
    {
        if($this->s_pages_model->item_exists($page_id))
        {
            $this->s_pages_model->set_item_data($page_id, array('index' => TRUE));
            $this->admin->form->message(__('message_8'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_8'), TRUE);
        }
        admin_redirect();
    }
    
    function unpublish_page($page_id = '')
    {
        if($this->s_pages_model->item_exists($page_id))
        {
            $this->s_pages_model->set_item_data($page_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_page($page_id = '')
    {
        if($this->s_pages_model->item_exists($page_id))
        {
            $this->s_pages_model->set_item_data($page_id, array('public' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect();
    }
    
    function add($page_type_id = '')
    {
        $this->_validation('', TRUE);
        
        if(form_sent())
        {
            $this->_page_type_variables_validation($this->input->post('page_type_id'), 'add');
        }
        
        if($this->admin->form->validate())
        {
            // Add page data
            $data = array();
            
            $data['parent_page_id'] = $this->input->post('parent_page_id');
            $data['name'] = $this->input->post('name');
            $data['page_type_id'] = $this->input->post('page_type_id');
            $data['_alias'] = $this->input->post('_alias');
            $data['public'] = is_form_true($this->input->post('public'));
            $data['_meta_title'] = $this->input->post('_meta_title');
            $data['_meta_description'] = $this->input->post('_meta_description');
            $data['_meta_keywords'] = $this->input->post('_meta_keywords');
            $data['changefreq'] = $this->input->post('changefreq');
            $data['index'] = ($this->input->post('index') == cfg('form', 'true'));
            $data['sitemap_priority'] = $this->input->post('sitemap_priority');
            $data['tpl'] = $this->input->post('tpl');
            
            if(strlen($data['_alias']) == 0) $data['_alias'] = url_title($data['name']);

            $this->s_pages_model->add_item($data);
            
            $page_id = $this->s_pages_model->insert_id();
            
            // Add page variables data
            $variables_data = array();
            
            foreach($this->cms->pages->get_page_type_variable_names($this->input->post('page_type_id'), 'add') as $variable_name)
            {
                $variables_data[$variable_name] = $this->input->post($variable_name);
            }
            
            $this->cms->pages->set_page_data($page_id, $variables_data);
            
            // Add to categories
            $status = TRUE;
            $in_categories = $this->input->post('in_categories');
            
            if(is_array($in_categories))
            {
                foreach($in_categories as $category_id)
                {
                    if($this->cms->categories->category_exists($category_id)) $this->cms->pages->add_page_to_category($page_id, $category_id);
                    else $status = FALSE;
                }
            }
            
            if($status) $this->admin->form->message(__('message_1'), TRUE);
            else $this->admin->form->error(__('error_10'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', '_alias', __('field_2'));
        $this->admin->form->add_field('select', 'parent_page_id', __('field_12'), $this->cms->pages->get_pages_select_data(), '', TRUE);
        $this->admin->form->add_field('multiple', 'in_categories', __('field_13'), $this->cms->categories->get_categories_select_data(), '', TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_11'), TRUE);
        $this->admin->form->add_field('select', 'page_type_id', __('field_3'), $this->s_page_types_model->get_data_in_col('name'), $page_type_id, TRUE);
        $this->admin->form->ajax_area('ajax_page_type_variables', array('page_type_id'));
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', '_meta_title', __('field_4'), '', __('placeholder_1'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_5'), '', __('placeholder_2'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_6'), '', __('placeholder_2'));
        $this->admin->form->add_field('select_changefreq', 'changefreq', __('field_7'), cfg('changefreq', 'default'));
        $this->admin->form->add_field('checkbox', 'index', __('field_8'), TRUE);
        $this->admin->form->add_field('slider', 'sitemap_priority', __('field_9'), cfg('sitemap', 'default_priority'), 0, 1, 0.1);
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('select', 'tpl', __('field_10'), $this->cms->templates->get_templates_select_data('pages', TRUE));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function ajax_page_type_variables()
    {
        if(!$this->input->is_ajax_request()) show_404();

        $data = array();
        
        $page_type_id = $this->input->post('page_type_id');
        
        if(strlen($page_type_id) == 0)
        {
            $data['result'] = TRUE;
            $data['content'] = '';
        }
        else
        {
            $data['result'] = $this->s_page_types_model->item_exists($page_type_id);
            $data['content'] = '';
            $data['error'] = __('error_9');

            if($data['result'])
            {
                $this->_page_type_variables_validation($page_type_id, 'add');
                if(form_sent()) $this->admin->form->validate();
                
                foreach($this->cms->pages->get_page_type_variable_ids($page_type_id, 'add') as $page_type_variable_id)
                {
                    $data['content'] .= $this->cms->pages->get_page_type_variable_field_row($page_type_variable_id);
                }
            }
        }
        
        $data['field_id'] = $this->admin->form->get_field_id(TRUE);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
    function edit($page_id = '')
    {
        if(!$this->s_pages_model->item_exists($page_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $page_type_id = $this->s_pages_model->$page_id->page_type_id;
        
        $this->_validation($page_id);
        $this->_page_type_variables_validation($page_type_id, 'edit');
        
        if($this->admin->form->validate())
        {
            // Edit page data
            $data = array();
            
            $data['parent_page_id'] = $this->input->post('parent_page_id');
            $data['name'] = $this->input->post('name');
            $data['_alias'] = $this->input->post('_alias');
            $data['public'] = is_form_true($this->input->post('public'));
            $data['_meta_title'] = $this->input->post('_meta_title');
            $data['_meta_description'] = $this->input->post('_meta_description');
            $data['_meta_keywords'] = $this->input->post('_meta_keywords');
            $data['changefreq'] = $this->input->post('changefreq');
            $data['index'] = ($this->input->post('index') == cfg('form', 'true'));
            $data['sitemap_priority'] = $this->input->post('sitemap_priority');
            $data['tpl'] = $this->input->post('tpl');
            
            if(strlen($data['_alias']) == 0) $data['_alias'] = url_title($data['name']);
            
            $this->s_pages_model->set_item_data($page_id, $data);
            
            // Edit page variables data
            $variables_data = array();
            
            foreach($this->cms->pages->get_page_type_variable_names($page_type_id, 'edit') as $variable_name)
            {
                $variables_data[$variable_name] = $this->input->post($variable_name);
            }
            
            $this->cms->pages->set_page_data($page_id, $variables_data);
            
            // Add to categories
            $status = TRUE;
            $page_category_ids = array();
            $in_categories = $this->input->post('in_categories');
            if(is_array($in_categories))
            {
                foreach($in_categories as $category_id)
                {
                    if($this->cms->categories->category_exists($category_id)) $page_category_ids[] = $category_id;
                    else $status = FALSE;
                }
            }
            
            $this->cms->pages->set_page_categories($page_id, $page_category_ids);
            
            if($status) $this->admin->form->message(__('message_2'), url_param() != 'accept');
            else $this->admin->form->error(__('error_11'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_pages_model->$page_id->name);
        $this->admin->form->add_field('input', '_alias', __('field_2'), $this->s_pages_model->$page_id->_alias);
        $this->admin->form->add_field('select', 'parent_page_id', __('field_12'), $this->cms->pages->get_pages_select_data($page_id), $this->s_pages_model->$page_id->parent_page_id, TRUE);
        $this->admin->form->add_field('multiple', 'in_categories', __('field_13'), $this->cms->categories->get_categories_select_data(), $this->cms->pages->get_page_categories($page_id), TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_11'), $this->s_pages_model->$page_id->public);
        $this->admin->form->add_field('adminbutton', 'page_types/edit/' . $page_type_id, __('field_3'), $this->s_page_types_model->$page_type_id->name, 'pencil');
        
        $page_data = $this->cms->pages->get_page_data($page_id);
                
        foreach($this->cms->pages->get_page_type_variables($page_type_id, 'edit') as $page_type_variable)
        {
            $variable_name = $page_type_variable->name;
            $this->cms->pages->add_page_type_variable_field($page_type_variable->id, @$page_data->$variable_name);
        }
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', '_meta_title', __('field_4'), $this->s_pages_model->$page_id->_meta_title, __('placeholder_1'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_5'), $this->s_pages_model->$page_id->_meta_description, __('placeholder_2'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_6'), $this->s_pages_model->$page_id->_meta_keywords, __('placeholder_2'));
        $this->admin->form->add_field('select_changefreq', 'changefreq', __('field_7'), $this->s_pages_model->$page_id->changefreq);
        $this->admin->form->add_field('checkbox', 'index', __('field_8'), $this->s_pages_model->$page_id->index);
        $this->admin->form->add_field('slider', 'sitemap_priority', __('field_9'), $this->s_pages_model->$page_id->sitemap_priority, 0, 1, 0.1);
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('select', 'tpl', __('field_10'), $this->cms->templates->get_templates_select_data('pages', TRUE), $this->s_pages_model->$page_id->tpl);
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_link(site_url(href_page($page_id)), __('button_13'), 'home');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($page_id = '')
    {
        if($this->s_pages_model->item_exists($page_id))
        {
            $this->s_pages_model->delete_item($page_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function duplicate($page_id = '')
    {
        if($this->cms->pages->duplicate($page_id))
        {
            $this->admin->form->message(__('message_6'), TRUE);
            admin_redirect();
        }
        else
        {
            $this->admin->form->error(__('error_6'), TRUE);
        }
        admin_redirect();
    }
    
    function export($page_id = '')
    {
        if($this->s_pages_model->item_exists($page_id))
        {
            $this->admin->form->warning("Stránky zatiaľ nie je možné exportovať.", TRUE);
            //$this->cms->export->page($page_id);
            //$this->cms->export->download();
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($page_id = '', $page_type_id_required = FALSE)
    {
        $parent_page_validation = (intval($page_id) > 0) ? 'parent_page_id[' . $page_id . ']' : 'item_exists_system[pages]';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        if($page_type_id_required)
        $this->admin->form->set_rules('page_type_id', __('field_3'), 'trim|required|item_exists_system[page_types]');
        $this->admin->form->set_rules('_alias', __('field_2'), 'trim|url_title|max_length[255]|valid_alias');
        $this->admin->form->set_rules('parent_page_id', __('field_12'), 'trim|' . $parent_page_validation);
        $this->admin->form->set_rules('public', __('field_11'), 'trim|intval');
        $this->admin->form->set_rules('_meta_title', __('field_4'), 'trim|max_length[255]');
        $this->admin->form->set_rules('_meta_description', __('field_5'), 'trim');
        $this->admin->form->set_rules('_meta_keywords', __('field_6'), 'trim');
        $this->admin->form->set_rules('changefreq', __('field_7'), 'trim|required|changefreq|max_length[255]');
        $this->admin->form->set_rules('index', __('field_8'), 'trim|intval');
        $this->admin->form->set_rules('sitemap_priority', __('field_9'), 'trim|required|sitemap_priority');
        $this->admin->form->set_rules('tpl', __('field_18'), 'trim|max_length[255]|tpl[pages]');
    }
    
    protected function _page_type_variables_validation($page_type_id = '', $type = '')
    {
        foreach($this->cms->pages->get_page_type_variable_ids($page_type_id, $type) as $page_type_variable_id)
        {
            $page_type_variable = $this->cms->pages->get_page_type_variable($page_type_variable_id);
            $this->admin->form->set_rules($page_type_variable->name, $page_type_variable->title, $page_type_variable->rules);
        }
    }
    
}