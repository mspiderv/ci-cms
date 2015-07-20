<!DOCTYPE HTML>
<html>
    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <?= generate('head') ?>
        
    </head>
    
    <body>

        <h3>CMS successfully installed !</h3>

        <h4>Administration</h4>
        <p>
            <strong>User:</strong> admin<br>
            <strong>Pass:</strong> system123
        </p>
        <p><a href="<?= base_url() ?>admin">Open administration</a></p>

        <h4>Configuration</h4>
        <p>Please <a href="<?= base_url() ?>admin/settings#tab-5">configure your new web</a> now.</p>

        <h4>File access</h4>
        <p>Dont forget to set 777 permissions.</p>

    </body>
</html>