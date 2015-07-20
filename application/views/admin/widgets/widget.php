<div class="col col_<?= $width ?> widget" id="<?= $id ?>"<?= (@$settings['collapsed']) ? 'data-collapsed="true"' : '' ?>>

    <div class="ui-widget-content widget_wrap">

        <h3 class="ui-widget-headera widget_heading">
            <?= $heading ?>
            <span class="widget_buttons">
                <a class="jui_icon ui-state-default button_collapse"><span class="ui-icon ui-icon-carat-2-n-s"></span></a>
                <a class="jui_icon ui-state-default button_close"><span class="ui-icon ui-icon ui-icon-closethick"></span></a>
            </span>
        </h3>

        <div class="widget_content"><?= $content ?></div>

    </div>

</div>