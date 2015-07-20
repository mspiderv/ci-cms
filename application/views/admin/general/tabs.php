<div class="jui_tabs jui_tabs_custom">
    <ul>
        <?php foreach($tabs as $tab_id => $tab): ?>
        <li><a href="#tab-<?= $tab_id ?>"><?= $tab['name'] ?></a></li>
        <?php endforeach ?>
    </ul>
    <?php foreach($tabs as $tab_id => $tab): ?>
    <div id="tab-<?= $tab_id ?>"><?= $tab['content'] ?></div>
    <?php endforeach ?>
</div>