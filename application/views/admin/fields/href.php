<div class="field_href" data-page="field_href" data-value-type="<?= $value_type ?>" data-value-value="<?= htmlspecialchars($value_value) ?>">
    
    <div class="href_type">
        
        <input type="hidden" name="<?= $name ?>" class="href_type_value fv" />
        
        <?= form_dropdown('', $selector_options, $value_type, $extra) ?>
        
    </div>
    
    <div class="href_type_types">
        
        <div class="field_type_wrap nomargin">
        
            <input type="hidden" value="" class="href_type_empty" />
        
        </div>
        
        <div class="field_type_wrap">
        
            <?= form_dropdown('', $pages, ($value_type == 'page' ? $value_value : ''), 'data-page="chosen" class="href_type_page a_select chosen"') ?>
        
        </div>
        
        <div class="field_type_wrap">
        
            <?= form_dropdown('', $products, ($value_type == 'product' ? $value_value : ''), 'data-page="chosen" class="href_type_product a_select chosen"') ?>
    
        </div>
        
        <div class="field_type_wrap">
        
            <?= form_dropdown('', $categories, ($value_type == 'category' ? $value_value : ''), 'data-page="chosen" class="href_type_category a_select chosen"') ?>

        </div>
        
        <div class="field_type_wrap">
        
            <?= form_dropdown('', $services, ($value_type == 'service' ? $value_value : ''), 'data-page="chosen" class="href_type_service a_select chosen"') ?>

        </div>
        
        <div class="field_type_wrap">
            
            <?= form_input('', ($value_type == 'url' ? $value_value : ''), 'class="href_type_url input"') ?>
            
        </div>
        
    </div>
    
    <?php if(count($attributes) > 0): ?>
    
        <div class="href_field_attributes" data-page="href_field_attributes"<?= (count($attrs) > 0) ? ' data-visible' : '' ?>>

            <p class="handle" data-show="<?= ll('field_href_attributes_show') ?>" data-hide="<?= ll('field_href_attributes_hide') ?>"></p>

            <div class="content">

                <?php foreach($attributes as $attribute_key => $attribute): ?>

                    <p class="attribute_wrap">
                        <strong><label for="attribute_<?= $field_id ?>_<?= $attribute_key ?>"><?= $attribute ?></label></strong>
                        <span class="attribute_field"><input type="text" data-name="<?= $attribute ?>" value="<?= htmlspecialchars(@$attrs[$attribute]) ?>" id="attribute_<?= $field_id ?>_<?= $attribute_key ?>" class="a_field a_input" /></span>
                    </p>

                <?php endforeach ?>

            </div>

        </div>
    
    <?php endif ?>
    
</div>