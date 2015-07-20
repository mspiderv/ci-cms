<?php foreach($contextmenu as $contextmenu_item_id => $contextmenu_item): ?>
    <ul id="contextmenu_<?= $contextmenu_item_id ?>" class="jeegoocontext cm_default">
        <?php foreach($contextmenu_item as $contextmenu_item_href): ?>
            <li 
                <?php if(strlen($contextmenu_item_href['icon']) > 0): ?>
                    data-icon="<?= $contextmenu_item_href['icon'] ?>" 
                <?php endif ?>
                data-href="<?= $contextmenu_item_href['href'] ?>"
                <?php if(strlen($contextmenu_item_href['data-text']) > 0): ?>
                    data-text="<?= $contextmenu_item_href['data-text'] ?>"
                <?php endif ?>
                <?php if(strlen($contextmenu_item_href['data-title']) > 0): ?>
                    data-title="<?= $contextmenu_item_href['data-title'] ?>"
                <?php endif ?>
            >
            <?= $contextmenu_item_href['text'] ?>
            </li>
        <?php endforeach ?>
    </ul>
<?php endforeach ?>