<table celpadding="0" cellspacing="0" border="0" class="datatable<?= ($sortable) ? ' datatable-sorting' : '' ?>" data-page="datatable contextmenu">
    <thead>
        <?php if(count((array)$cols)): ?>
            <tr>
                <?php foreach((array)$cols as $col): ?>
                    <th
                        <?= (strlen($col['width'])) ? ' style="width: ' . $col['width'] . 'px!important;"' : '' ?>
                        <?= (strlen($col['help'])) ? ' data-page="tiptip" class="tiptip" title="' . $col['help'] . '"' : '' ?>
                    ><?= $col['title'] ?></th>
                <?php endforeach; ?>
            </tr>
        <?php endif ?>
    </thead>
    <tbody>
        
        <?php foreach((array)@$rows as $row): ?>

            <tr
                <?= (strlen($row['id'])) ? ' id="' . $row['id'] . '"' : '' ?>
                <?= (strlen($row['sortgroup'])) ? ' data-sortgroup="' . $row['sortgroup'] . '"' : '' ?>
                <?= (strlen($row['sortgroup'])) ? ' data-table="' . (($row['sortgroup_categorizing']) ? implode('_', array_slice(explode('_', $row['sortgroup']), 0, -1)) : $row['sortgroup']) . '"' : '' ?>
                <?= (strlen($row['classes'])) ? ' class="' . $row['classes'] . '"' : '' ?>
                <?= ($row['contextmenu']) ? 'data-page="contextmenu" data-contextmenu="' . $row['sortgroup'] . '_' . $row['id'] . '"' : '' ?>
            >
                
                <?php 
                
                $i = 0;
                foreach((array)$row['cells'] as $cell):
                $i++;
                
                ?>

                    <td
                        <?= ($listing_level_col == $i && strlen($row['level'])) ? ' data-level="' . $row['level'] . '"' : '' ?>
                        <?= (strlen($cell['classes'])) ? ' class="' . $cell['classes'] . '"' : '' ?>
                    >
                        
                        <?php if($listing_level_col == $i && strlen($row['sortgroup'])): ?>
                            <span class="handle ui-icon ui-icon-carat-2-n-s"></span>
                        <?php endif ?>
                        
                        <?= $cell['content'] ?>
                            
                    </td>

                <?php endforeach ?>
                
            </tr>

        <?php endforeach; ?>
        
    </tbody>
</table>