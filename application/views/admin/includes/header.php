<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8" />
<title><?= $title ?></title>
<?php $this->carabiner->display('admin_css'); ?>
<script>

    var cms = {};

    cms.lang = "<?= lang() ?>";
    cms.admin_lang = "<?= admin_user_lang() ?>";
    cms.base_url = "<?= base_url() ?>";
    cms.assets = "<?= site_url(cfg('folder', 'assets')) ?>/";
    cms.assets_relative = "/<?= cfg('folder', 'assets') ?>/";
    cms.ajax = "<?= site_url(cfg('url', 'ajax')) ?>/";
    cms.admin = "<?= site_url(cfg('url', 'admin')) ?>/";
    cms.logout = "<?= site_url(cfg('url', 'admin')) ?>/login/logout";
    cms.csrf_token_name = "<?= cfg('csrf_token_name') ?>";
    cms.url_param = "<?= cfg('url', 'param') ?>";
    cms.ckeditor_skin = '<?= addslashes($ckeditor_skin) ?>';
    cms.eshop = <?= (cfg('general', 'eshop')) ? 'true' : 'false' ?>;
    cms.url_iframe = '<?= addslashes(cfg('url', 'iframe')) ?>';
    cms.url_true = '<?= addslashes(cfg('url', 'true')) ?>';
    
    cms.panels = [];
    <?php foreach($js_panels as $panel_id => $panel_name): ?>
    cms.panels.push(['{<?= cfg('parser_val', 'panel') ?>:<?= $panel_id ?>}', '<?= addslashes($panel_name) ?>']);
    <?php endforeach ?>
    
    cms.positions = [];
    <?php foreach($js_positions as $position_id => $position_name): ?>
    cms.positions.push(['{<?= cfg('parser_val', 'position') ?>:<?= $position_id ?>}', '<?= addslashes($position_name) ?>']);
    <?php endforeach ?>
    
    cms.href_pages = [];
    <?php foreach($js_pages as $page_id => $page_name): ?>
    cms.href_pages.push(['<?= addslashes($page_name) ?>', '<?= cfg('parser_val', 'href') . ':page:' . intval($page_id) ?>']);
    <?php endforeach ?>
    
    cms.href_products = [];
    <?php foreach($js_products as $product_id => $product_name): ?>
    cms.href_products.push(['<?= addslashes($product_name) ?>', '<?= cfg('parser_val', 'href') . ':product:' . intval($product_id) ?>']);
    <?php endforeach ?>
    
    cms.href_product_categories = [];
    <?php foreach($js_categories as $category_id => $category_name): ?>
    cms.href_product_categories.push(['<?= addslashes($category_name) ?>', '<?= cfg('parser_val', 'href') . ':category:' . intval($category_id) ?>']);
    <?php endforeach ?>
        
    cms.href_services = [];
    <?php foreach($js_services as $service_id => $service_name): ?>
    cms.href_services.push(['<?= addslashes($service_name) ?>', '<?= cfg('parser_val', 'href') . ':service:' . intval($service_id) ?>']);
    <?php endforeach ?>
    
    cms.langs = [];
    <?php foreach($js_langs as $lang_code => $lang_name): ?>
    cms.langs.push(['<?= addslashes($lang_name) ?>', '<?= addslashes($lang_code) ?>']);
    <?php endforeach ?>
    
    cms.$_GET = <?= json_encode($_GET) ?>;
    cms.$_POST = <?= json_encode($_POST) ?>;
    cms.$_REQUEST = <?= json_encode($_REQUEST) ?>;
    
    delete cms.$_GET[cms.csrf_token_name];
    delete cms.$_POST[cms.csrf_token_name];
    delete cms.$_REQUEST[cms.csrf_token_name];
    
    config = {};
    config.form_sent = <?= json_encode(cfg('form', 'sent')) ?>;
    
    window.CKEDITOR_BASEPATH = cms.assets + "<?= cfg('folder', 'admin') ?>/ckeditor/";

</script>
<?php if(!is_localhost()): ?><script src="http://code.jquery.com/jquery-1.7.1.min.js"></script><?php endif ?>
<script>!window.jQuery && document.write('<script src="<?= $this->carabiner->script_uri ?>admin/js/jquery-1.7.1.min.js"><\/script>')</script>
<?php $this->carabiner->display('admin_js'); ?>
<link rel="shortcut icon" href="<?= ADMIN_ASSETS ?>admin/data/favicon.ico" />
<script type="text/javascript">
<?php foreach($charts as $chart_id => $chart_data): ?>
charts_data[<?= json_encode($chart_id) ?>] = <?= $chart_data ?>;
<?php endforeach ?>
</script>
</head>