<div class="box" id="vrch">
    
    <a href="<?= admin_url() ?>"><img src="<?= ADMIN_ASSETS ?>admin/data/logo.png" height="21" alt="Webvitamin CMS" class="logo"></a>
    <h1><a href="<?= site_url() ?>"><?= cfg('general', 'app_name') ?></a></h1>
    <p class="version"><a href="<?= admin_url('updates') ?>"><strong><?= ll('admin_cms_version') ?></strong> <?= strtoupper(cfg('cms', 'type')) ?> - <?= cfg('cms', 'version') ?></a></p>
    
</div>