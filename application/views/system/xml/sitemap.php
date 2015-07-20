<?= '<?' ?>xml version="1.0" encoding="utf-8"<?= '?>' ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($items as $item): if(@$item['url'] == '') continue; ?>
    <url><loc><?= $item['url'] ?></loc>
        <?php if(strlen(@$item['lastmod']) > 0): ?><lastmod><?= date('Y-m-d', $item['lastmod']) ?></lastmod><?php endif ?>
        <?php if(strlen(@$item['changefreq']) > 0): ?><changefreq><?= $item['changefreq'] ?></changefreq><?php endif ?>
        <?php if(strlen(@$item['priority']) > 0): ?><priority><?= $item['priority'] ?></priority><?php endif ?>
    </url>
<?php endforeach ?>
</urlset>