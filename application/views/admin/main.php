<?= $this->admin->load_view('includes/header', $GLOBALS) ?>

<body>
    
    <?= $this->admin->load_view('includes/contextmenu', $GLOBALS) ?>

    <?= form_open($form_action, array('id' => 'form', 'class' => (@$form_validation_error ? 'validation_error' : '') . (form_sent() ? ' form_sent' : '')), $hidden_fields) ?>

        <div id="wrap">

            <?= $top ?>
            
            <?= $menu ?>
            
            <div id="main">
                
                <?php if(strlen($messages) || strlen($warnings) || strlen($errors)): ?>

                    <div class="reports" data-page="reports">

                        <?= $messages ?>
                        <?= $warnings ?>
                        <?= $errors ?>

                    </div>

                <?php endif ?>
                
                <?= $strips ?>
                
                <?= $content ?>
                
            </div>
            
            <div id="overlay" data-page="overlay"><span class="overlay_close">Zavrieť</span></div>
            
            <div id="popup"></div>

            <div id="elfinder" data-page="elfinder"></div>
            
            <ul id="board" class="disableselection" data-page="disableselection"></ul>

        </div>

        <?= $this->admin->load_view('includes/no_js') ?>

        <div data-page="tabs"></div>

        <div id="hide" style="display: none;"></div>

    </form>

</body>
</html>