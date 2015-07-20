<div class="stripe">
    
    <div class="left breadcrumbs">
        
        <a href="<?= admin_url() ?>" class="home"><span class="icon"></span></a>
        
        <?php 
        
        $breadcrumbs = '';
        $first = TRUE;
        
        foreach($links as $link)
        {
            if($first) $first = FALSE;
            else $breadcrumbs .= '<span class="separator"></span>';
            $breadcrumbs .= '<a href="' . $link['href'] . '">' . $link['text'] . '</a>';
        }
        
        echo $breadcrumbs;
        
        ?>
        
    </div>
    
    <?php if(strlen($buttons)): ?>
        <div class="right buttons">
            <?= $buttons ?>
        </div>
    <?php endif ?>
    
</div>