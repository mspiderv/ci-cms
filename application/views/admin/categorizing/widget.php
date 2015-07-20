<div class="category ui-widget" id="<?= $id ?>">

    <h4 class="ui-widget-header ui-corner-all"><?= $name ?></h4>

    <ul class="list sortable droppable">
        
        <?php foreach($items as $item): ?>

            <li class="item-categorized ui-state-default ui-corner-all" id="<?= $item['id'] ?>"><?= $item['name'] ?></li>

        <?php endforeach ?>
        
    </ul>

</div>