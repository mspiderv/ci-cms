<p class="left"><?= $dropdown ?></p>

<p class="left link_to_gallery" data-page="field_product_gallery">
    
    <?= admin_anchor(
            'iframe:',
            '<span class="ui-icon ui-icon-image"></span>',
            '',
            array(
                'id' => $field_id . '_button',
                'class' => 'jui_icon ui-state-default ui-corner-all tiptip field_product_gallery_button',
                'title' => ll('admin_general_edit_gallery'),
                'data-page' => 'tiptip',
                'data-tip' => 'right',
                'data-href' => admin_url('eshop/product_galleries/edit') . '/'
            )
        ) ?>
    
</p>