<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Database_deposit {

    // Členské premenné
    protected $CI;
    protected $backups;
    protected $backups_path;

    /* Konštruktor */
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->backups_path = APPPATH . cfg('database_backup', 'folder') . '/';
    }
    
    function backup()
    {
        $cache_on = $this->CI->db->cache_on;
        $this->CI->db->cache_on = FALSE;
        
        $this->CI->load->helper('file');
        $this->CI->load->dbutil();
        $this->CI->load->helper('file');
        
        $backup_params = array(
            'format' => 'txt',
            'ignore' => array(cfg('sess_table_name'))
        );
        
        $backup =& $this->CI->dbutil->backup($backup_params);
        
        $dirpath = APPPATH . cfg('database_backup', 'folder') . '/';
        $path = $dirpath . date(cfg('database_backup', 'backup_filename')) . '.' . cfg('database_backup', 'backup_filename_format');
        
        if(is_really_writable($dirpath))
        {
            write_file($path, $backup);
            $this->CI->db->cache_on = $cache_on;
            return TRUE;
        }
        else
        {
            $this->CI->db->cache_on = $cache_on;
            return FALSE;
        }
    }
    
    function get_backups()
    {
        if(!is_array($this->backups))
        {
            $this->CI->load->helper('directory');

            foreach((array)directory_map($this->backups_path, 1) as $backup)
            {
                if(get_ext($backup) != cfg('database_backup', 'backup_filename_format')) continue;
                $this->backups[] = cut_ext($backup);
            }
        }
        
        return (array)$this->backups;
    }
    
    function backup_exists($backup = '')
    {
        return in_array($backup, $this->get_backups());
    }
    
    function read($backup = '')
    {
        if($this->backup_exists($backup))
        {
            $this->CI->load->helper('file');
            return read_file($this->backups_path . $backup . '.' . cfg('database_backup', 'backup_filename_format'));
        }
        else
        {
            return FALSE;
        }
    }
    
    function write($backup = '', $data = '')
    {
        $this->CI->load->helper('file');
        return write_file($this->backups_path . $backup . '.' . cfg('database_backup', 'backup_filename_format'), $data, 'w');
    }
    
    function delete_backup($backup = '')
    {
        if($this->backup_exists($backup))
        {
            return @unlink($this->backups_path . $backup . '.' . cfg('database_backup', 'backup_filename_format'));
        }
        else
        {
            return FALSE;
        }
    }
    
    function delete_all_backups()
    {
        $this->CI->load->helper('file');
        return delete_files($this->backups_path);
    }
    
    function restore($file = '')
    {
        // Zmaže cache (cahe)
        $this->CI->load->driver('cache', array('adapter' => 'file'));
        $this->CI->cache->clean();
        
        // Zmaže cache (db_cahe)
        $this->CI->load->helper('file');
        delete_files($this->CI->db->cachedir);
        
        $cache_on = $this->CI->db->cache_on;
        $this->CI->db->cache_on = FALSE;
        
        $path = APPPATH . cfg('database_backup', 'folder') . '/' . $file . '.' . cfg('database_backup', 'backup_filename_format');
        
        if(file_exists($path))
        {
            $this->CI->db->query('SET foreign_key_checks = 0');

            // Vytvorenie novej zálohy
            $this->backup();
            
            // Zmazanie súčasnej databázy
            $this->CI->load->dbforge();
            foreach($this->CI->db->list_tables() as $table_name)
            {
                if($table_name == cfg('sess_table_name')) continue;
                $this->CI->dbforge->drop_table(substr($table_name, strlen($this->CI->db->dbprefix)));
            }
            
            // Importovanie databázy zo zálohy
            foreach(explode_sql(read_file($path)) as $sql)
            {
                if(trim($sql) != '') $this->CI->db->query($sql);
            }
            
            $this->CI->db->query('SET foreign_key_checks = 1');
            $this->CI->db->cache_on = $cache_on;
            return TRUE;
        }
        else
        {
            $this->CI->db->cache_on = $cache_on;
            return FALSE;
        }
    }
    
}