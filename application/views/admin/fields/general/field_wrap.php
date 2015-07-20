<tr class="tr_field<?= (strlen(@$class) > 0) ? ' ' . $class : '' ?><?php if(strlen($error) > 0): ?> tr_field_error<?php endif ?>" data-page="required_fields">
    <th<?php if($multilingual): ?> class="multilingual tiptip" title="<?= ll('admin_general_multilang_field') ?>" data-page="tiptip" data-tip="right"<?php endif ?>>
        <span class="title"><label for="<?= $field_id ?>"><?= $title ?></label></span>
        <?php if(strlen($info) > 0): ?><span class="info"><?= $info ?></span><?php endif ?>
    </th>
    <td>
        <div class="field_wrap" data-label="<?= $label ?>" data-rules="<?= $rules ?>"><?= $field ?></div>
        <?php if(strlen($error) > 0): ?><?= $error ?><?php endif ?>
    </td>
</tr>