<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Updates extends CI_Driver {
    
    protected $CI;
    protected $type;
    protected $version;
    protected $server;
    protected $updates = array();
    protected $downloaded_packages = NULL;
    protected $extracted_packages = NULL;
    protected $unextracted_packages = NULL;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->load->helper('file');
        $this->CI->load->helper('directory');
        
        $this->initiaize();
    }
    
    function initiaize()
    {
        $this->type = cfg('cms', 'type');
        $this->version = cfg('cms', 'version');
        $this->server = prep_url(cfg('cms', 'server'));
        
        if(substr($this->server, -1) != '/') $this->server .= '/';
    }
    
    function is_updated()
    {
        return (intval(@file_get_contents($this->server . 'is_updated.php?type=' . $this->type . '&version=' . $this->version)) > 0);
    }
    
    function get_updates($version = NULL)
    {
        if(!isset($this->updates['version_' . $version]) || !is_array($this->updates['version_' . $version]))
        {
            $updates = json_decode(@file_get_contents($this->server . 'get_updates.php?type=' . $this->type . '&version=' . (($version == NULL) ? $this->version : $version)));
            $this->updates['version_' . $version] = (is_array($updates)) ? $updates : array();
        }
        
        return $this->updates['version_' . $version];
    }
    
    function check_version($version = '')
    {
        return (in_array($version, $this->get_updates(cfg('cms', 'version'))));
    }
    
    function get_package_name($type = '', $from = '', $to = '', $with_ext = TRUE)
    {
        $package_name = $type . '-' . $from . '--' . $to;
        $package_name = str_replace('.', '-', $package_name);
        $package_name = sanitize_file_name($package_name);
        return ($with_ext) ? $package_name . '.' . cfg('update_package', 'format') : $package_name;
    }
    
    function get_downloaded_packages()
    {
        if(!is_array($this->downloaded_packages))
        {
            $this->downloaded_packages = array();
            
            $files = directory_map(cfg('path', 'update_packages'), 1);
            
            if(is_array($files))
            {
                foreach($files as $filename)
                {
                    if(get_ext($filename) == cfg('update_package', 'format')) $this->downloaded_packages[] = cut_ext($filename);
                }
            }
        }
        
        return $this->downloaded_packages;
    }
    
    function package_is_downloaded($type_or_package_name = '', $from = '', $to = '')
    {
        $package_name = (strlen($from) > 0 && strlen($to) > 0) ? $this->get_package_name($type_or_package_name, $from, $to, FALSE) : cut_ext($type_or_package_name);
        return (in_array($package_name, $this->get_downloaded_packages()));
    }
    
    function download_package($type_or_package_name = '', $from = '', $to = '')
    {
        $ext = '.' . cfg('update_package', 'format');
        $package_name = (strlen($from) > 0 && strlen($to) > 0) ? $this->get_package_name($type_or_package_name, $from, $to, FALSE) : cut_ext($type_or_package_name);
        if($this->package_is_downloaded($package_name)) return TRUE;
        $url = cfg('cms', 'server') . 'updates/' . $package_name . $ext;
        if(!($data = @file_get_contents($url))) return FALSE;
        if(write_file(cfg('path', 'update_packages') . $package_name . $ext, $data))
        {
            $this->downloaded_packages = NULL;
            $this->unextracted_packages = NULL;
            return TRUE;
        }
        else return FALSE;
        
    }
    
    function get_package_full_path($type_or_package_name = '', $from = '', $to = '')
    {
        return $this->get_package_full_path_dir($type_or_package_name, $from, $to) . '.' . cfg('update_package', 'format');
    }
    
    function get_package_full_path_dir($type_or_package_name = '', $from = '', $to = '')
    {
        $package_name = (strlen($from) > 0 && strlen($to) > 0) ? $this->get_package_name($type_or_package_name, $from, $to, FALSE) : cut_ext($type_or_package_name);
        return cfg('path', 'update_packages') . $package_name;
    }
    
    function extract_package($type_or_package_name = '', $from = '', $to = '')
    {
        $package_name = (strlen($from) > 0 && strlen($to) > 0) ? $this->get_package_name($type_or_package_name, $from, $to, FALSE) : cut_ext($type_or_package_name);
        if($this->package_is_extracted($package_name)) return TRUE;
        if(!$this->download_package($package_name)) return FALSE;
        $extract_to = $this->get_package_full_path_dir($package_name);
        @mkdir($extract_to);
        @chmod($extract_to, 0777);
        $this->CI->load->library('unzip');
        if($this->CI->unzip->extract($this->get_package_full_path($package_name), $extract_to))
        {
            $this->extracted_packages = NULL;
            $this->unextracted_packages = NULL;
            return TRUE;
        }
        else return FALSE;
    }
    
    function get_extracted_packages()
    {
        if(!is_array($this->extracted_packages))
        {
            $this->extracted_packages = array();
            
            $files = directory_map(cfg('path', 'update_packages'), 1);
            
            if(is_array($files))
            {
                foreach($files as $filename)
                {
                    if(is_dir(cfg('path', 'update_packages') . $filename)) $this->extracted_packages[] = $filename;
                }
            }
        }
        
        return $this->extracted_packages;
    }

    function package_is_extracted($type_or_package_name = '', $from = '', $to = '')
    {
        $package_name = (strlen($from) > 0 && strlen($to) > 0) ? $this->get_package_name($type_or_package_name, $from, $to, FALSE) : cut_ext($type_or_package_name);
        return (in_array($package_name, $this->get_extracted_packages()));
    }
    
    function get_unextracted_packages()
    {
        if(!is_array($this->unextracted_packages))
        {
            $this->unextracted_packages = array_diff($this->get_downloaded_packages(), $this->get_extracted_packages());
        }
        
        return $this->unextracted_packages;
    }
    
    function delete_extracted_package($type_or_package_name = '', $from = '', $to = '')
    {
        $package_name = (strlen($from) > 0 && strlen($to) > 0) ? $this->get_package_name($type_or_package_name, $from, $to, FALSE) : cut_ext($type_or_package_name);
        if(!$this->package_is_extracted($package_name)) return TRUE;
        return delete_dir($this->get_package_full_path_dir($package_name));
    }
    
    function delete_downloaded_package($type_or_package_name = '', $from = '', $to = '')
    {
        $package_name = (strlen($from) > 0 && strlen($to) > 0) ? $this->get_package_name($type_or_package_name, $from, $to, FALSE) : cut_ext($type_or_package_name);
        if(!$this->package_is_downloaded($package_name)) return TRUE;
        return @unlink($this->get_package_full_path($package_name));
    }
    
    function delete_package($type_or_package_name = '', $from = '', $to = '')
    {
        $package_name = (strlen($from) > 0 && strlen($to) > 0) ? $this->get_package_name($type_or_package_name, $from, $to, FALSE) : cut_ext($type_or_package_name);
        return ($this->delete_downloaded_package($package_name) && $this->delete_extracted_package($package_name));
    }
    
    function check_package($package_name = '', $type = '', $from = '', $to = '')
    {
        if(!$this->package_is_extracted(cut_ext($package_name))) return FALSE;
        
        $info = read_file($this->get_package_full_path_dir($package_name) . '/' . cfg('update_package', 'info_file'));
        if(!$info) return FALSE;
        $info = json_decode($info);
        return(
            json_last_error() == 0
            &&
            @$info['type'] != $type
            &&
            @$info['version_from'] != $from
            &&
            @$info['version_to'] != $to
        );
    }
    
    function _configure_system($type = '', $version = '', $server = '')
    {
        $config_file = cfg('file', 'config_system');
        
        if(!is_really_writable($config_file)) return FALSE;
        
        $data = array();
        
        $data['type'] = $type;
        $data['version'] = $version;
        $data['server'] = prep_url($server);
        
        if(substr($data['server'], -1) != '/') $data['server'] = $data['server'] . '/';
        
        return write_file($config_file, $this->load_system_view('config/system', $data, TRUE));
    }
    
    function update($version = '')
    {
        $version = trim($version);
        
        $package_name = $this->get_package_name($this->type, $this->version, $version);
        if(!$this->extract_package($package_name)) return FALSE;
        $package_dir = $this->get_package_full_path_dir($package_name) . '/';
        
        $status = TRUE;
        
        // Replace files
        $dir_replace = $package_dir . cfg('update_package', 'dir_replace') . '/';
        if(is_dir($dir_replace))
        {
            
            
            $target_dir = APPPATH . 'test'; // TODO: DEBUG: CONTINUE: dat tam retazec './' a odtestovat, ale POZOR lebo mi to moze prepisat funkcne subory
            
            
            if(!dir_is_really_writable($target_dir)) return FALSE;
            if(!dir_copy($dir_replace, $target_dir)) $status = FALSE;
        }
        
        // SQL
        $dir_sql = $package_dir . cfg('update_package', 'dir_sql') . '/';
        $sqls = directory_map($dir_sql, 1);

        $sql_files = array();
        
        if(is_array($sqls))
        {
            foreach($sqls as $sql)
            {
                if(get_ext($sql) == 'sql') $sql_files[] = $sql;
            }
        }
        
        sort($sql_files, SORT_STRING);

        $db_debug = $this->CI->db->db_debug;
        $this->CI->db->db_debug = FALSE;
        
        foreach($sql_files as $sql_file)
        {
            foreach(explode_sql(read_file($dir_sql . '/' . $sql_file)) as $sql)
            {
                if(trim($sql) != '' && !$this->CI->db->query($sql)) $status = FALSE;
            }
        }
        
        $this->CI->db->db_debug = $db_debug;
        
        // Scripts
        $dir_scripts = $package_dir . cfg('update_package', 'dir_scripts') . '/';
        
        $scripts = directory_map($dir_scripts, 1);

        $script_files = array();
        
        if(is_array($scripts))
        {
            foreach($scripts as $script)
            {
                if(get_ext($script) == 'php') $script_files[] = $script;
            }
        }
        
        sort($script_files, SORT_STRING);
        
        foreach($script_files as $script_file)
        {
            if(!include_once($dir_scripts . '/' . $script_file)) $status = FALSE;
        }
        
        // Update version
        if($status && !$this->_configure_system($this->type, $version, $this->server)) $status = FALSE;
        
        // Delete update package
        if(!$this->delete_package($package_name)) $status = FALSE;
        
        // Return status
        return $status;
    }
    
}