<script>
    
    // CMS
    var BASE = '<?= addslashes(BASE) ?>';
    var ASSETS = '<?= addslashes(ASSETS) ?>';
    var FILES = '<?= addslashes(FILES) ?>';
    var THEME_ASSETS = '<?= addslashes(THEME_ASSETS) ?>';
    
    var cms = {};

    cms.base_url = '<?= THEME_ASSETS ?>';
    cms.action_url = '<?= href_action() . '/' ?>';
    cms.lang = '<?= lang() ?>';
    
</script>