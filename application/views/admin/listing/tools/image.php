<p class="cell_image">
    
    <?php if($is_href): ?><a href="<?= admin_url($href)?>"><?php endif ?>
    
        <img src="<?= $image_url ?>" alt="" style="max-width: <?= $max_width ?>px; max-height: <?= $max_height ?>px;" <?= $attributes ?> />
    
    <?php if($is_href): ?></a><?php endif ?>
    
</p>