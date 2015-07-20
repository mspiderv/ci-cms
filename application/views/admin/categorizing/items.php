<div class="items">

    <?php foreach($items as $item_id => $item_name): ?>
    
        <p class="item jui_button ui-state-default ui-corner-all draggable" data-id="<?= $item_id ?>"><?= $item_name ?></p>
    
    <?php endforeach ?>

</div>

<div class="clear"></div>