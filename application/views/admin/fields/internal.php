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
        
    </div>
    
</div>