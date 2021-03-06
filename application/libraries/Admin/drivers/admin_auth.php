<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_auth extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    protected $permission_name;
    protected $user_id;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->library('phpass');
        $this->CI->cms->model->load_admin('users');
        
        $this->permission_name = $this->CI->router->fetch_class() . '/' . $this->CI->router->fetch_method();
    }
    
    function is_logged()
    {
        return $this->user_exists($this->get_user_id());
    }
    
    function get_user_id()
    {
        if(intval($this->user_id) == 0)
        {
            $this->user_id = $this->get_session_user_id();
        }
        
        return $this->user_id;
    }
    
    function get_user_lang($user_id = '')
    {
        if($this->CI->a_users_model->item_exists($user_id))
        {
            //$user_lang = $this->CI->a_users_model->$user_id->lang;
            $user_lang = $this->CI->a_users_model->get_item_data($user_id, 'lang');
            if(strlen($user_lang)) return $user_lang;
        }
        
        return default_admin_lang();
    }
    
    function get_user_name($user_id = '')
    {
        if($this->CI->a_users_model->item_exists($user_id))
        {
            return $this->CI->a_users_model->get_item_data($user_id, 'name');
        }
        else
        {
            return '';
        }
    }
    
    function get_user_cookie_login($user_id = '')
    {
        return ($this->CI->a_users_model->item_exists($user_id)) ? (bool)$this->CI->a_users_model->$user_id->cookie_login : FALSE;
    }
    
    function get_usernames()
    {
        return $this->CI->a_users_model->get_data_in_col('name');
    }
    
    function get_session_user_id()
    {
        return $this->CI->session->userdata(cfg('session_keys', 'admin_user_id'));
    }
    
    function unset_session_user_id()
    {
        $this->CI->session->unset_userdata(cfg('session_keys', 'admin_user_id'), 0);
    }
    
    function set_session_user_id($user_id = '')
    {
        $this->unset_session_user_id();
        if(!$this->user_exists($user_id)) return FALSE;
        $this->CI->session->set_userdata(cfg('session_keys', 'admin_user_id'), $user_id);
    }
    
    function set_user_login_data($user_id = '', $name = '', $password = '')
    {
        if($this->CI->a_users_model->item_exists($user_id))
        {
            $login_data = array();
            
            $login_data['name'] = $name;
            $login_data['password'] = $this->CI->phpass->hash($password);
            
            $this->CI->a_users_model->set_item_data($user_id, $login_data);
            
            return TRUE;
        }
    }
    
    function set_user_lang($user_id = '', $lang = '')
    {
        if($this->CI->a_users_model->item_exists($user_id) && in_array($lang, cfg('admin_languages')))
        {
            $this->CI->a_users_model->set_item_data($user_id, array('lang' => $lang));
            return TRUE;
        }
        
        return FALSE;
    }
    
    function get_all_permissions()
    {
        $this->CI->load->helper('directory');
        $this->CI->load->helper('file');
        
        $permissions = array();
        $permissions['*'] = $this->get_permission_name('*');
        
        $path = APPPATH . 'controllers/' . cfg('folder', 'admin');
        
        foreach((array)directory_map($path, 1) as $item)
        {
            $controller_name = cut_ext($item);
            $permissions[$controller_name . '/*'] = $this->get_permission_name($controller_name . '/*');
            
            $result = array();
            $controller_content = read_file($path . '/' . $item);
            preg_match_all('/function (.*)\(/', $controller_content, $result);
            
            foreach((array)$result[1] as $function)
            {
                if(substr($function, 0, 1) != '_')
                {
                    $permissions[$controller_name . '/' . $function] = $this->get_permission_name($controller_name . '/' . $function);
                }
            }
        }
        
        return $permissions;
    }
    
    function get_permission_name($permission = '')
    {
        load_lang('admin/general');
        
        if($permission == '*') return ll('admin_general_all');
        
        @list($controller, $method) = @explode('/', $permission);
        load_lang('admin/' . $controller);
        
        $controller_title = ll('admin_' . $controller . '_title');
        if($controller_title == '') $controller_title = $controller;
        
        if($method == '*') $method_title = ll('admin_general_all');
        elseif($method == 'index') $method_title = ll('admin_general_index');
        else $method_title = ll('admin_' . $controller . '_title_' . $method);
        
        if($method_title == '') $method_title = $method;
        
        return $controller_title . ' - ' . $method_title;
    }
    
    function user_exists($user_id = '')
    {
        return $this->CI->a_users_model->item_exists($user_id);
    }
    
    function check_login_data($name = '', $password = '')
    {
        foreach($this->CI->a_users_model->get_data() as $user)
        {
            if($user->name == $name && $this->CI->phpass->check($password, $user->password))
            {
                return $user->id;
            }
        }
        
        return FALSE;
    }
    
    function check_user_password($user_id = '', $password = '')
    {
        if($this->user_exists($user_id)) return ($this->CI->phpass->check($password, $this->CI->a_users_model->get_item_data($user_id, 'password')));
        else return FALSE;
    }
    
    function check_access()
    {
        if($this->is_logged())
        {
            // Logged
            if(!$this->user_has_permission($this->get_user_id(), $this->permission_name))
            {
                // Access denied
                show_error('Access denied', 401);
            }
        }
        else
        {
            // Unlogged
            
            // Try to log via cookie values
            
            $cookie_name = $this->get_cookie_name();
            $cookie_password = $this->get_cookie_password();
            
            $user_id = $this->check_login_data($cookie_name, $cookie_password);
            
            if($user_id && $this->get_user_cookie_login($this->get_user_id_by_name($cookie_name)))
            {
                // Cookie login successful
                $this->set_session_user_id($user_id);
            }
            else
            {
                // Cookie login failure
                $temp_url = $this->get_temp_url();
                if(strlen($temp_url) > 0 && $this->CI->router->fetch_method() == 'login') $this->keep_temp_url();
                else $this->set_temp_url(uri_string());
                admin_redirect('login');
            }
        }
    }
    
    function set_temp_url($url = '')
    {
        $this->CI->session->set_userdata(cfg('session_keys', 'admin_temp_url'), $url);
    }
    
    function get_temp_url()
    {
        return $this->CI->session->userdata(cfg('session_keys', 'admin_temp_url'));
    }
    
    function keep_temp_url()
    {
        return $this->CI->session->userdata(cfg('session_keys', 'admin_temp_url'));
    }
    
    function unset_temp_url()
    {
        return $this->CI->session->unset_userdata(cfg('session_keys', 'admin_temp_url'), array());
    }
    
    function user_name_used($name = '')
    {
        return in_array($name, $this->CI->a_users_model->get_data_in_col('name'));
    }
    
    function create_user($name = '', $password = '', $lang = '', $permissions = array(), $cookie_login = FALSE, $hash_password = TRUE)
    {
        if(count((array)$permissions) == 0)
        {
            $permissions = array($this->CI->router->default_controller . '/index');
        }
        
        if($lang == '')
        {
            $lang = default_admin_lang();
        }
        
        if(!$this->user_name_used($name))
        {
            $user_data = array();
            
            $user_data['name'] = $name;
            $user_data['password'] = ($hash_password) ? $this->CI->phpass->hash($password) : $password;
            $user_data['permissions'] = serialize((array)$permissions);
            $user_data['last_login'] = 0;
            $user_data['registration_time'] = time();
            $user_data['cookie_login'] = (bool)$cookie_login;
            $user_data['lang'] = $lang;
            
            return $this->CI->a_users_model->add_item($user_data);
        }
        else
        {
            return FALSE;
        }
    }
    
    function duplicate_user($user_id = '')
    {
        if($this->user_exists($user_id))
        {
            $name =  $this->create_new_user_name($this->CI->a_users_model->get_item_data($user_id, 'name'));
            $password = $this->CI->a_users_model->get_item_data($user_id, 'password');
            $lang = $this->CI->a_users_model->$user_id->lang;
            
            return $this->create_user($name, $password, $lang, $this->get_user_permissions($user_id), ($this->CI->a_users_model->$user_id->cookie_login > 0), FALSE);
        }
        else
        {
            return FALSE;
        }
    }
    
    function create_new_user_name($name = '')
    {
        $names = $this->get_usernames();
        while(in_array($name, $names)) $name .= ' - copy';
        return $name;
    }
    
    function update_login_time()
    {
        if($this->is_logged())
        {
            $data = array();
            $data['last_login'] = time();
            $this->CI->a_users_model->set_item_data($this->get_user_id(), $data);
        }
    }
    
    function delete_user($user_id = '')
    {
        if($this->user_exists($user_id))
        {
            $this->CI->a_users_model->delete_item($user_id);
        }
    }
    
    function get_user_id_by_name($user_name = '')
    {
        $users = array_flip($this->CI->a_users_model->get_data_in_col('name'));
        return (isset($users[$user_name]) ? $users[$user_name] : FALSE);
    }
    
    function user_has_permission($user_id = '', $permission_name = '')
    {
        if(in_array('*', $this->get_user_permissions($user_id))) return TRUE;
        if(in_array($this->CI->router->fetch_class() . '/*', $this->get_user_permissions($user_id))) return TRUE;
        
        if($permission_name != '*' && !strpos($permission_name, '/')) $permission_name .= '/index';
        
        return in_array($permission_name, $this->get_user_permissions($user_id));
    }
    
    function user_really_has_permission($user_id = '', $permission_name = '')
    {
        if($permission_name != '*' && !strpos($permission_name, '/')) $permission_name .= '/index';
        return in_array($permission_name, $this->get_user_permissions($user_id));
    }
    
    function get_user_permissions($user_id = '')
    {
        if($this->CI->a_users_model->item_exists($user_id))
        {
            return (array)unserialize($this->CI->a_users_model->$user_id->permissions);
        }
        else
        {
            return array();
        }
    }
    
    function set_user_cookie_login($user_id = '', $cookie_login = FALSE)
    {
        if($this->CI->a_users_model->item_exists($user_id))
        {
            $this->CI->a_users_model->set_item_data($user_id, array('cookie_login' => (bool)$cookie_login));
        }
    }
    
    function set_user_permissions($user_id = '', $permissions = array())
    {
        if($this->CI->a_users_model->item_exists($user_id))
        {
            foreach($permissions as $permission_key => $permission_name)
            {
                if($permission_name != '*' && !strpos($permission_name, '/'))
                {
                    $permissions[$permission_key] = $permission_name . '/index';
                }
            }
            
            $this->CI->a_users_model->set_item_data($user_id, array('permissions' => serialize((array)$permissions)));
        }
    }
    
    function set_user_permission($user_id = '', $permission = '', $access = FALSE)
    {
        if($this->CI->a_users_model->item_exists($user_id))
        {
            if($permission != '*' && !strpos($permission, '/')) $permission .= '/index';
            
            $user_permissions = (array)$this->get_user_permissions($user_id);
            
            if($access && !in_array($permission, $user_permissions))
            {
                $user_permissions[] = $permission;
            }
            elseif(!$access)
            {
                $user_permissions = remove_from_array($user_permissions, $permission);
            }
            
            return $this->set_user_permissions($user_id, $user_permissions);
        }
    }
    
    function get_cookie_name()
    {
        return $this->CI->input->cookie(cfg('cookie_keys', 'admin_login_name'), TRUE);
    }
    
    function get_cookie_password()
    {
        return $this->CI->input->cookie(cfg('cookie_keys', 'admin_login_password'));
    }
    
    function set_cookie_data($name = '', $password = '')
    {
        $cookie = array(
            'name'   => cfg('cookie_keys', 'admin_login_name'),
            'value'  => $name,
            'expire' => cfg('expire', 'admin_remember_login')
        );

        $this->CI->input->set_cookie($cookie);
        
        $cookie = array(
            'name'   => cfg('cookie_keys', 'admin_login_password'),
            'value'  => $password,
            'expire' => cfg('expire', 'admin_remember_login')
        );

        $this->CI->input->set_cookie($cookie);
    }
    
    function delete_cookie_data()
    {
        $cookie = array(
            'name'   => cfg('cookie_keys', 'admin_login_name'),
            'value'  => '',
            'expire' => '0'
        );

        $this->CI->input->set_cookie($cookie);
        
        $cookie = array(
            'name'   => cfg('cookie_keys', 'admin_login_password'),
            'value'  => '',
            'expire' => '0'
        );

        $this->CI->input->set_cookie($cookie);
    }
    
}