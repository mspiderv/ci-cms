<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['useragent'] = '{{SITE}}';
$config['protocol'] = 'sendmail';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['smtp_host'] = 'smtp.websupport.sk';
$config['smtp_user'] = 'info@{{SITE}}';
$config['smtp_pass'] = '';
$config['smtp_port'] = 25;
$config['smtp_timeout'] = 5;
$config['wordwrap'] = TRUE;
$config['wrapchars'] = 76;
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['validate'] = FALSE;
$config['priority'] = 3;
$config['crlf'] = '\n';
$config['newline'] = '\n';
$config['bcc_batch_mode'] = FALSE;
$config['bcc_batch_size'] = 200;

/* End of file autoload.php */
/* Location: ./application/config/email.php */