<?= $this->admin->load_view('includes/header', $GLOBALS) ?>

<body>

    <?= form_open('', array('id' => 'form', 'class' => (@$form_validation_error ? 'validation_error' : '') . (form_sent() ? ' form_sent' : '')), $hidden_fields) ?>

        <div id="wrap">
            
            <div id="main">
                
                <div id="login">

                    <p class="heading ui-widget-header">
                        <?= __('heading') ?>
                    </p>
                    <p class="field">
                        <label for="name"><?= __('name') ?>:</label>
                        <input type="text" name="login_name" class="input<?= (strlen($default_name) > 0 && strlen($default_password) > 0) ? '' : ' autofocus' ?>" id="name" value="<?= set_value('login_name', $default_name) ?>"<?= (strlen($default_name) > 0 && strlen($default_password) > 0) ? '' : ' data-page="autofocus"' ?> />
                    </p>
                    <p class="field">
                        <label for="password"><?= __('password') ?>:</label>
                        <input type="password" name="login_password" class="input" id="password" value="<?= (!form_sent()) ? $default_password : '' ?>" />
                    </p>
                    <?php if($login_error): ?>
                        <p class="error">
                            <?= __('error') ?>
                        </p>
                    <?php endif ?>
                    <p class="submit">
                        <!--
                        <span class="left">
                            <?= form_checkbox('remember', cfg('form', 'true'), ((form_sent()) ? (set_value('remember') == cfg('form', 'true')) : (strlen($default_name) > 0 && strlen($default_password) > 0)), 'id="remember_field"') ?>
                            <label for="remember_field"><?= __('remember') ?></label>
                        </span>
                        -->
                        <span class="right">
                            <a class="jui_button ui-state-default form_submit_e ui-corner-all"><span class="ui-icon ui-icon-power"></span><?= __('submit') ?></a>
                        </span>
                        <span class="clear block"></span>
                    </p>

                </div>

            </div>

        </div>
    
    </form>

    <?= $this->admin->load_view('includes/no_js') ?>

</body>
</html>