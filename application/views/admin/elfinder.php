<?= $this->admin->load_view('includes/header') ?>

<body>
       
    <div id="wrap">

        <?= $top ?>
            
        <?= $menu ?>
        
        <div class="elfinder_autoload" data-page="elfinder_autoload" data-ckeditorfuncnum="<?= $this->input->get('CKEditorFuncNum') ?>"></div>

    </div>
    
    <?= $this->admin->load_view('includes/no_js') ?>

</body>
</html>